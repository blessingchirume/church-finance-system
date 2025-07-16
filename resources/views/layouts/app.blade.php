<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Tailwind CSS via CDN (for demo purposes, use proper build process in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-gray-100">
<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and brand name -->
                <div class="flex items-center space-x-2">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-8 w-auto rounded-full">
                        <span class="ml-2 text-xl font-bold text-gray-900 hidden md:inline">Foundation of Hope</span>
                    </a>
                </div>

                <!-- Primary navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('members.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Families</a>
                    <a href="{{ route('members.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Members</a>
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Projects</a>
                    <a href="{{ route('services.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Services</a>
                    <a href="{{ route('expenses.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Expenses</a>
                    <a href="{{ route('incomes.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Income</a>
                    <a href="{{ route('members.index') }}" class="text-gray-600 hover:bg-gray-100 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Departments</a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Hamburger icon -->
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu (hidden by default) -->
        <div class="md:hidden hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Families</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Members</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Projects</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Services</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Expenses</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Income</a>
                <a href="{{ route('members.index') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300">Departments</a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{  asset('assets/js/jquery-3.6.0.min.js') }}" ></script>
<script>
    // alert($('#memberSearch').html())
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('memberSearch');
        const searchResults = document.getElementById('searchResults');
        const memberForm = document.getElementById('memberForm');
        const memberIdInput = document.getElementById('member_id');
        const submitButton = document.getElementById('submitButton');

        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function () {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    func.apply(context, args);
                }, wait);
            };
        }

        // Handle search input
        searchInput.addEventListener('input', debounce(function () {
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            var url = `http:127.0.0.1:1992/member/search?q=${encodeURIComponent(query)}`
            // alert(url)
            {{--fetch(`{{ route('members.search') }}?q=${encodeURIComponent(query)}`)--}}
            $.ajax({
                url: `{{ url('/member/search') }}?q=${encodeURIComponent(query)}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        let html = '';
                        $.each(data, function(index, member) {
                            html += `
                <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 member-result"
                     data-id="${member.id}"
                     data-name="${member.name}"
                     data-email="${member.email || ''}"
                     data-phone="${member.phone || ''}"
   data-address="${member.address || ''}"
                     data-status="${member.status}">
                    ${member.name} - ${member.phone || ''}
                </div>`;
                        });

                        $(searchResults).html(html).removeClass('hidden');

                        // Add click event to results
                        $(searchResults).on('click', '.member-result', function() {
                            const $this = $(this);
                            const memberId = $this.data('id');
                            const memberName = $this.data('name');
                            const memberEmail = $this.data('email');
                            const memberPhone = $this.data('phone');
                            const memberAddress = $this.data('address');
                            const memberStatus = $this.data('status');

                            // Update form values
                            $(memberIdInput).val(memberId);
                            $('#name').val(memberName);
                            $('#email').val(memberEmail);
                            $('#phone').val(memberPhone);
                            $('#address').val(memberAddress);
                            $('#status').val(memberStatus);

                            // Update form action and button text
                            $(memberForm).attr('action', `{{ url('/members') }}/${memberId}`)
                                .find('input[name="_method"]').remove()
                                .end()
                                .append('<input type="hidden" name="_method" value="PUT">');

                            $(submitButton).text('Update Member');

                            // Hide results and clear search
                            $(searchResults).addClass('hidden');
                            $(searchInput).val('');
                        });
                    } else {
                        $(searchResults).html('<div class="p-2 text-gray-500">No members found. Continue to create new member.</div>')
                            .removeClass('hidden');

                        // Reset form for new member
                        resetFormForNewMember();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error fetching members: ' + error);
                }
            });
        }, 300));

        // Reset form when clicking outside search results
        document.addEventListener('click', function (e) {
            if (!searchResults.contains(e.target) && e.target !== searchInput) {
                searchResults.classList.add('hidden');
            }
        });

        // Function to reset form for new member creation
        function resetFormForNewMember() {
            memberIdInput.value = '';
            memberForm.action = "{{ route('members.store') }}";
            memberForm.querySelector('input[name="_method"]')?.remove();
            submitButton.textContent = 'Save Member';
        }

        // Allow manual form submission for new members
        memberForm.addEventListener('submit', function (e) {
            // Form will submit normally
        });
    });
</script>
</body>
</html>
