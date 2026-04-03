// fcm.js

// 1️⃣ Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyD-LjO3dsAUkruYXEZU0l4BLS1LKzFXCyQ",
    authDomain: "fama-10735.firebaseapp.com",
    projectId: "fama-10735",
    storageBucket: "fama-10735.firebasestorage.app",
    messagingSenderId: "1014441330386",
    appId: "1:1014441330386:web:1e8c8da5343990e483cf0b",
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// 2️⃣ Register SW, then wait for it to be fully ACTIVE before doing anything
navigator.serviceWorker.register('/firebase-messaging-sw.js', {
        scope: '/'
    })
    .then((registration) => {
        console.log('Service Worker registered:', registration.scope);

        // If already active, go straight to permission
        if (registration.active) {
            requestPermissionAndSendToken(registration);
            return;
        }

        // Otherwise wait for the SW to finish installing → activating
        const sw = registration.installing || registration.waiting;
        sw.addEventListener('statechange', (e) => {
            if (e.target.state === 'activated') {
                requestPermissionAndSendToken(registration);
            }
        });
    })
    .catch((err) => {
        console.error('Service Worker registration failed:', err);
    });

// 3️⃣ Request permission and get token
// ✅ Accepts the registration and passes it explicitly to getToken()
async function requestPermissionAndSendToken(swRegistration) {
    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            console.log('Notification permission denied');
            return;
        }

        // ✅ Pass swRegistration so FCM uses the SW we KNOW is active
        const token = await messaging.getToken({
            vapidKey: 'BO50uWHMmyJY-TaKTpprGOsMIQqRKrYRKOkikvshO99HYaYnxnRokDlPSMMGyoAGUfL-yXKdDfqMAlzeymLEdXw',
            serviceWorkerRegistration: swRegistration,
        });

        console.log(token);

        console.log('FCM Token:', token);

        await fetch(window.routes.saveFcmToken, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                token: token,
                device_name: navigator.userAgent,
            }),
        });

        console.log('Token sent to backend');
    } catch (err) {
        console.error('Error getting FCM token:', err);
    }
}



messaging.onMessage((payload) => {
    console.log('Message received in foreground:', payload);

    // ✅ Guard: check permission first
    // if (Notification.permission !== 'granted') {
    //     console.warn('Notification permission not granted, skipping.');
    //     return;
    // }

    // const title = payload.notification?.title || 'New Notification';
    // const options = {
    //     body: payload.notification?.body || '',
    //     icon: '/images/fg.png',
    //     badge: '/images/fg.png',
    //     data: payload.data || {},
    // };

    // // ✅ Use navigator.serviceWorker.ready instead of getRegistration()
    // // .ready always resolves with the ACTIVE SW — getRegistration() can return undefined
    // navigator.serviceWorker.ready
    //     .then((registration) => {
    //         registration.showNotification(title, options);
    //     })
    //     .catch((err) => {
    //         console.error('showNotification failed:', err);

    //         // Fallback: use basic Notification API directly
    //         new Notification(title, options);
    //     });
});