import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:uuid/uuid.dart';

void main() => runApp(const SundayCaptureApp());

class SundayCaptureApp extends StatelessWidget {
  const SundayCaptureApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Sunday Capture',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF146C5C)),
        useMaterial3: true,
      ),
      home: const CaptureHomePage(),
    );
  }
}

class CaptureHomePage extends StatefulWidget {
  const CaptureHomePage({super.key});

  @override
  State<CaptureHomePage> createState() => _CaptureHomePageState();
}

class _CaptureHomePageState extends State<CaptureHomePage> {
  final _uuid = const Uuid();
  final _loginFormKey = GlobalKey<FormState>();
  final _transactionFormKey = GlobalKey<FormState>();
  final _baseUrlController = TextEditingController(text: 'http://10.0.2.2:8000');
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _purposeController = TextEditingController();
  final _amountController = TextEditingController();
  final _currencyController = TextEditingController(text: 'USD');
  final _notesController = TextEditingController();

  SharedPreferences? _prefs;
  String? _token;
  String? _userName;
  bool _busy = true;
  String? _message;
  int? _assemblyId;
  int? _accountId;
  String _flow = 'offerings';
  String _paymentMethod = 'cash';
  DateTime _selectedDate = DateTime.now();
  List<Map<String, dynamic>> _assemblies = [];
  List<Map<String, dynamic>> _accounts = [];
  List<TransactionDraft> _drafts = [];
  List<Map<String, dynamic>> _recent = [];

  @override
  void initState() {
    super.initState();
    _restore();
  }

  @override
  void dispose() {
    _baseUrlController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _purposeController.dispose();
    _amountController.dispose();
    _currencyController.dispose();
    _notesController.dispose();
    super.dispose();
  }

  bool get _isExpense => _flow == 'expenses';

  String get _apiBase => _baseUrlController.text.replaceAll(RegExp(r'/+$'), '');

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  Future<void> _restore() async {
    final prefs = await SharedPreferences.getInstance();
    _prefs = prefs;
    _token = prefs.getString('token');
    _userName = prefs.getString('user_name');
    _baseUrlController.text = prefs.getString('base_url') ?? _baseUrlController.text;
    _assemblies = _decodeList(prefs.getString('assemblies'));
    _accounts = _decodeList(prefs.getString('accounts'));
    _drafts = _decodeList(prefs.getString('drafts')).map(TransactionDraft.fromJson).toList();
    if (_assemblies.isNotEmpty) {
      _assemblyId = _assemblies.first['id'] as int;
    }
    if (_token != null) {
      await _loadReferenceData();
      await _loadRecent();
    }
    setState(() => _busy = false);
  }

  List<Map<String, dynamic>> _decodeList(String? raw) {
    if (raw == null || raw.isEmpty) return [];
    return (jsonDecode(raw) as List).cast<Map<String, dynamic>>();
  }

  Future<void> _saveLocal() async {
    final prefs = _prefs;
    if (prefs == null) return;
    await prefs.setString('base_url', _baseUrlController.text);
    if (_token != null) await prefs.setString('token', _token!);
    if (_userName != null) await prefs.setString('user_name', _userName!);
    await prefs.setString('assemblies', jsonEncode(_assemblies));
    await prefs.setString('accounts', jsonEncode(_accounts));
    await prefs.setString('drafts', jsonEncode(_drafts.map((draft) => draft.toJson()).toList()));
  }

  Future<void> _login() async {
    if (!_loginFormKey.currentState!.validate()) return;
    setState(() {
      _busy = true;
      _message = null;
    });
    try {
      final response = await http.post(
        Uri.parse('$_apiBase/api/mobile/login'),
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        body: jsonEncode({
          'email': _emailController.text.trim(),
          'password': _passwordController.text,
          'device_name': 'Sunday Capture Phone',
        }),
      );
      if (response.statusCode >= 400) {
        throw Exception(_errorMessage(response));
      }
      final body = jsonDecode(response.body) as Map<String, dynamic>;
      _token = body['token'] as String;
      _userName = body['user']['name'] as String;
      _assemblies = (body['assemblies'] as List).cast<Map<String, dynamic>>();
      _assemblyId = _assemblies.isEmpty ? null : _assemblies.first['id'] as int;
      await _loadReferenceData();
      await _loadRecent();
      await _saveLocal();
    } catch (error) {
      _message = 'Login failed: $error';
    } finally {
      setState(() => _busy = false);
    }
  }

  Future<void> _loadReferenceData() async {
    final accountsResponse = await http.get(
      Uri.parse('$_apiBase/api/mobile/chart-accounts'),
      headers: _headers,
    );
    if (accountsResponse.statusCode < 400) {
      _accounts = (jsonDecode(accountsResponse.body)['data'] as List).cast<Map<String, dynamic>>();
      _selectFirstMatchingAccount();
    }
  }

  Future<void> _loadRecent() async {
    final response = await http.get(
      Uri.parse('$_apiBase/api/mobile/transactions/recent'),
      headers: _headers,
    );
    if (response.statusCode < 400) {
      _recent = (jsonDecode(response.body)['data'] as List).cast<Map<String, dynamic>>();
    }
  }

  void _selectFirstMatchingAccount() {
    final matches = _accounts.where((account) => account['type'] == (_isExpense ? 'expense' : 'income')).toList();
    _accountId = matches.isEmpty ? null : matches.first['id'] as int;
  }

  Future<void> _saveDraft({String status = 'Draft'}) async {
    if (!_transactionFormKey.currentState!.validate()) return;
    final draft = TransactionDraft(
      localId: _uuid.v4(),
      assemblyId: _assemblyId!,
      date: _dateOnly(_selectedDate),
      type: _isExpense ? 'expense' : 'income',
      flow: _flow,
      chartAccountId: _accountId!,
      categoryPurpose: _purposeController.text.trim(),
      amount: double.parse(_amountController.text),
      currency: _currencyController.text.trim().toUpperCase(),
      paymentMethod: _paymentMethod,
      notes: _notesController.text.trim(),
      status: status,
    );
    setState(() {
      _drafts.insert(0, draft);
      _message = 'Draft saved locally.';
    });
    _clearTransactionForm();
    await _saveLocal();
  }

  Future<void> _syncPending() async {
    if (_token == null) return;
    setState(() {
      _busy = true;
      _message = null;
    });
    for (final draft in _drafts.where((draft) => draft.status != 'Synced')) {
      draft.status = 'Pending Sync';
      await _saveLocal();
      try {
        final response = await http.post(
          Uri.parse('$_apiBase/api/mobile/transactions'),
          headers: _headers,
          body: jsonEncode(draft.toApiJson()),
        );
        if (response.statusCode >= 400) {
          throw Exception(_errorMessage(response));
        }
        draft.status = 'Synced';
      } catch (error) {
        draft.status = 'Failed';
        draft.error = error.toString();
      }
    }
    await _loadRecent();
    await _saveLocal();
    setState(() {
      _busy = false;
      _message = 'Sync completed.';
    });
  }

  String _errorMessage(http.Response response) {
    try {
      final body = jsonDecode(response.body) as Map<String, dynamic>;
      return body['message']?.toString() ?? response.body;
    } catch (_) {
      return 'HTTP ${response.statusCode}';
    }
  }

  String _dateOnly(DateTime date) {
    final month = date.month.toString().padLeft(2, '0');
    final day = date.day.toString().padLeft(2, '0');
    return '${date.year}-$month-$day';
  }

  void _clearTransactionForm() {
    _purposeController.clear();
    _amountController.clear();
    _notesController.clear();
  }

  Future<void> _logout() async {
    await _prefs?.remove('token');
    setState(() {
      _token = null;
      _userName = null;
      _recent = [];
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_busy) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }
    return _token == null ? _loginScreen() : _captureScreen();
  }

  Widget _loginScreen() {
    return Scaffold(
      appBar: AppBar(title: const Text('Sunday Capture')),
      body: SafeArea(
        child: Form(
          key: _loginFormKey,
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              TextFormField(
                controller: _baseUrlController,
                decoration: const InputDecoration(labelText: 'Cloud system URL', prefixIcon: Icon(Icons.cloud_outlined)),
                validator: _required,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(labelText: 'Email', prefixIcon: Icon(Icons.email_outlined)),
                keyboardType: TextInputType.emailAddress,
                validator: _required,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _passwordController,
                decoration: const InputDecoration(labelText: 'Password', prefixIcon: Icon(Icons.lock_outline)),
                obscureText: true,
                validator: _required,
              ),
              const SizedBox(height: 16),
              FilledButton.icon(onPressed: _login, icon: const Icon(Icons.login), label: const Text('Login')),
              if (_message != null) Padding(padding: const EdgeInsets.only(top: 16), child: Text(_message!)),
            ],
          ),
        ),
      ),
    );
  }

  Widget _captureScreen() {
    final matchingAccounts = _accounts.where((account) => account['type'] == (_isExpense ? 'expense' : 'income')).toList();
    return Scaffold(
      appBar: AppBar(
        title: Text(_userName ?? 'Sunday Capture'),
        actions: [
          IconButton(onPressed: _logout, icon: const Icon(Icons.logout), tooltip: 'Logout'),
        ],
      ),
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            await _loadReferenceData();
            await _loadRecent();
            await _saveLocal();
            setState(() {});
          },
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Form(
                key: _transactionFormKey,
                child: Column(
                  children: [
                    DropdownButtonFormField<int>(
                      value: _assemblyId,
                      decoration: const InputDecoration(labelText: 'Assembly', prefixIcon: Icon(Icons.account_balance_outlined)),
                      items: _assemblies
                          .map((assembly) => DropdownMenuItem<int>(value: assembly['id'] as int, child: Text(assembly['name'].toString())))
                          .toList(),
                      onChanged: (value) => setState(() => _assemblyId = value),
                      validator: (value) => value == null ? 'Required' : null,
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(child: Text('Date: ${_dateOnly(_selectedDate)}')),
                        IconButton(
                          onPressed: () async {
                            final picked = await showDatePicker(
                              context: context,
                              initialDate: _selectedDate,
                              firstDate: DateTime(2020),
                              lastDate: DateTime.now().add(const Duration(days: 30)),
                            );
                            if (picked != null) setState(() => _selectedDate = picked);
                          },
                          icon: const Icon(Icons.calendar_month),
                          tooltip: 'Pick date',
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<String>(
                      value: _flow,
                      decoration: const InputDecoration(labelText: 'Flow', prefixIcon: Icon(Icons.category_outlined)),
                      items: const [
                        DropdownMenuItem(value: 'offerings', child: Text('Offerings')),
                        DropdownMenuItem(value: 'pledges', child: Text('Pledges')),
                        DropdownMenuItem(value: 'funeral_contributions', child: Text('Funeral contributions')),
                        DropdownMenuItem(value: 'general_income', child: Text('General income')),
                        DropdownMenuItem(value: 'expenses', child: Text('Expenses')),
                      ],
                      onChanged: (value) {
                        setState(() {
                          _flow = value!;
                          _selectFirstMatchingAccount();
                        });
                      },
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<int>(
                      value: matchingAccounts.any((account) => account['id'] == _accountId) ? _accountId : null,
                      decoration: const InputDecoration(labelText: 'G/L Account', prefixIcon: Icon(Icons.account_balance_outlined)),
                      items: matchingAccounts
                          .map((account) => DropdownMenuItem<int>(
                                value: account['id'] as int,
                                child: Text('${account['code']} - ${account['name']}'),
                              ))
                          .toList(),
                      onChanged: (value) => setState(() => _accountId = value),
                      validator: (value) => value == null ? 'Required' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _purposeController,
                      decoration: const InputDecoration(labelText: 'Category / Purpose', prefixIcon: Icon(Icons.label_outline)),
                      validator: _required,
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(
                          flex: 2,
                          child: TextFormField(
                            controller: _amountController,
                            decoration: const InputDecoration(labelText: 'Amount', prefixIcon: Icon(Icons.payments_outlined)),
                            keyboardType: const TextInputType.numberWithOptions(decimal: true),
                            validator: (value) {
                              if (value == null || value.trim().isEmpty) return 'Required';
                              return double.tryParse(value) == null ? 'Invalid amount' : null;
                            },
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: TextFormField(
                            controller: _currencyController,
                            decoration: const InputDecoration(labelText: 'Currency'),
                            validator: (value) => value == null || value.trim().length != 3 ? 'Use 3 letters' : null,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    DropdownButtonFormField<String>(
                      value: _paymentMethod,
                      decoration: const InputDecoration(labelText: 'Payment method', prefixIcon: Icon(Icons.point_of_sale_outlined)),
                      items: const [
                        DropdownMenuItem(value: 'cash', child: Text('Cash')),
                        DropdownMenuItem(value: 'ecocash', child: Text('EcoCash')),
                        DropdownMenuItem(value: 'bank_transfer', child: Text('Bank transfer')),
                        DropdownMenuItem(value: 'card', child: Text('Card')),
                        DropdownMenuItem(value: 'other', child: Text('Other')),
                      ],
                      onChanged: (value) => setState(() => _paymentMethod = value!),
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _notesController,
                      decoration: const InputDecoration(labelText: 'Notes', prefixIcon: Icon(Icons.notes_outlined)),
                      maxLines: 2,
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: () => _saveDraft(),
                            icon: const Icon(Icons.save_outlined),
                            label: const Text('Save Draft'),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: FilledButton.icon(
                            onPressed: _syncPending,
                            icon: const Icon(Icons.sync),
                            label: const Text('Sync Pending'),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              if (_message != null) Padding(padding: const EdgeInsets.only(top: 16), child: Text(_message!)),
              const SizedBox(height: 24),
              Text('Local records', style: Theme.of(context).textTheme.titleMedium),
              const SizedBox(height: 8),
              ..._drafts.map(_draftTile),
              const SizedBox(height: 24),
              Text('Recent submitted', style: Theme.of(context).textTheme.titleMedium),
              const SizedBox(height: 8),
              ..._recent.map(_recentTile),
            ],
          ),
        ),
      ),
    );
  }

  Widget _draftTile(TransactionDraft draft) {
    return Card(
      child: ListTile(
        leading: Icon(draft.status == 'Synced' ? Icons.cloud_done_outlined : Icons.cloud_upload_outlined),
        title: Text('${draft.categoryPurpose} - ${draft.currency} ${draft.amount.toStringAsFixed(2)}'),
        subtitle: Text('${draft.date} • ${draft.flowLabel} • ${draft.status}${draft.error == null ? '' : '\n${draft.error}'}'),
        isThreeLine: draft.error != null,
      ),
    );
  }

  Widget _recentTile(Map<String, dynamic> record) {
    return Card(
      child: ListTile(
        leading: Icon(record['record_type'] == 'expense' ? Icons.trending_down : Icons.trending_up),
        title: Text('${record['category_purpose']} - ${record['currency']} ${record['amount']}'),
        subtitle: Text('${record['assembly']} • ${record['status']}'),
      ),
    );
  }

  String? _required(String? value) => value == null || value.trim().isEmpty ? 'Required' : null;
}

class TransactionDraft {
  TransactionDraft({
    required this.localId,
    required this.assemblyId,
    required this.date,
    required this.type,
    required this.flow,
    required this.chartAccountId,
    required this.categoryPurpose,
    required this.amount,
    required this.currency,
    required this.paymentMethod,
    required this.notes,
    required this.status,
    this.error,
  });

  final String localId;
  final int assemblyId;
  final String date;
  final String type;
  final String flow;
  final int chartAccountId;
  final String categoryPurpose;
  final double amount;
  final String currency;
  final String paymentMethod;
  final String notes;
  String status;
  String? error;

  String get flowLabel => flow.replaceAll('_', ' ');

  factory TransactionDraft.fromJson(Map<String, dynamic> json) {
    return TransactionDraft(
      localId: json['local_id'] as String,
      assemblyId: json['assembly_id'] as int,
      date: json['date'] as String,
      type: json['type'] as String,
      flow: json['flow'] as String,
      chartAccountId: json['chart_account_id'] as int,
      categoryPurpose: json['category_purpose'] as String,
      amount: (json['amount'] as num).toDouble(),
      currency: json['currency'] as String,
      paymentMethod: json['payment_method'] as String,
      notes: json['notes'] as String? ?? '',
      status: json['status'] as String,
      error: json['error'] as String?,
    );
  }

  Map<String, dynamic> toJson() => {
        'local_id': localId,
        'assembly_id': assemblyId,
        'date': date,
        'type': type,
        'flow': flow,
        'chart_account_id': chartAccountId,
        'category_purpose': categoryPurpose,
        'amount': amount,
        'currency': currency,
        'payment_method': paymentMethod,
        'notes': notes,
        'status': status,
        'error': error,
      };

  Map<String, dynamic> toApiJson() => {
        'mobile_client_id': localId,
        'assembly_id': assemblyId,
        'date': date,
        'type': type,
        'flow': flow,
        'chart_account_id': chartAccountId,
        'category_purpose': categoryPurpose,
        'amount': amount,
        'currency': currency,
        'payment_method': paymentMethod,
        'notes': notes,
      };
}
