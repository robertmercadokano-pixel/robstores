PHONE AUTH INTEGRATION ADVICE
-----------------------------
Files added:
- firebase.js         (Firebase config & init)
- phone-auth.js       (phone auth logic: sendOTP, verifyOTP)
- phone-auth.html     (standalone page for create-account with OTP via SMS)

What I changed/added:
- Added firebase.js with the config you provided (placed at project root).
- Added phone-auth.js which uses Firebase Phone Auth (compat API) and reCAPTCHA (invisible).
- Added phone-auth.html as an easy-to-test page. After successful verification it redirects to index.html.

Next steps you (or I can do next) may want:
- Replace your existing create-account page with the contents of phone-auth.html,
  or include the phone number fields + scripts into your existing create-account page.
- Remove any Google/Facebook auth code and script tags (I did NOT remove them automatically to avoid breaking unknown flows).
- Ensure in Firebase Console -> Authentication -> Sign-in method the 'Phone' provider is enabled.
- When testing on localhost, Firebase may restrict phone auth; for development you may need to add test phone numbers
  under Authentication -> Sign-in method -> Phone -> 'Phone numbers for testing' (recommended) to avoid using real SMS credits.
- For production, make sure your domain is authorized in Firebase Console -> Authentication -> Authorized domains.

How to test quickly:
1. Open phone-auth.html in a browser (via localhost).
2. Enter a phone number (in dev use a test number configured in Firebase console or a real number).
3. Click Send OTP, enter the code, Verify.
4. On success, you'll be redirected to index.html.

Note: I added these files and did NOT remove any existing signin implementations automatically. If you want,
I can now try to locate and remove Gmail/Facebook auth code from the project files and integrate the phone flow directly into your create-account page.
