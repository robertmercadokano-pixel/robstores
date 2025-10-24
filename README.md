# Rob's Bagstore - Multi-file Web App (Firebase-enabled)

## What's included
- Static demo products (immediately available)
- Optional Firestore integration: when Firestore is configured, products/orders sync to Firestore
- Google, Facebook, and Phone Auth using Firebase Auth
- Pages: index.html, home.html, shop.html, cart.html, checkout.html, profile.html
- CSS and JS modular files in `css/` and `js/`

## Quick start (local testing)
1. Unzip the project.
2. Serve it with a local static server (recommended):
   - `npx serve` or `python -m http.server 8000`
   - Open http://localhost:8000 in your browser.
3. Open `index.html` and sign in.

## Firebase setup checklist (do these in the Firebase Console)
1. Create a Firebase project (you already gave the firebaseConfig used in `js/firebase.js`).
2. In Authentication > Sign-in method: enable Google, Facebook, and Phone.
   - For Facebook, add the OAuth redirect URI shown in the Firebase console to your Facebook App settings.
3. In Authentication > Authorized domains, add `localhost` while testing.
4. (Optional) Create a Firestore database (Start in test mode while developing).

## Notes
- The provided `firebaseConfig` is embedded in `js/firebase.js`.
- For production, secure Firestore rules and Facebook App settings (privacy policy, domain).
- See `js/shop.js` for the code that uses both local demo products and Firestore products (it prefers Firestore when available).
