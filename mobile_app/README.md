# Sunday Capture Mobile App

Minimal Flutter companion app for church officers to capture Sunday income and expense transactions on a phone, save local drafts, and sync pending records to the Laravel cloud API.

## API Base URL

The login screen asks for the API base URL. Use the Laravel host without a trailing `/api`, for example:

- Android emulator: `http://10.0.2.2:8000`
- iOS simulator: `http://127.0.0.1:8000`
- Physical phone: `http://<your-computer-lan-ip>:8000`

## Running Later

Flutter commands were intentionally not run during scaffolding. From this folder, run:

```bash
flutter pub get
flutter run
```

If your Flutter install requires platform folders first, run `flutter create .` in this folder once, keeping the existing `lib/main.dart` and `pubspec.yaml`.
