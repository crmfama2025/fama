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

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// 2️⃣ Register service worker
navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
        console.log('Service Worker registered:', registration.scope);
        requestPermissionAndSendToken();
    })
    .catch((err) => {
        console.error('Service Worker registration failed:', err);
    });

// 3️⃣ Request permission and get token
async function requestPermissionAndSendToken() {
    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            console.log('Notification permission denied');
            return;
        }

        const token = await messaging.getToken({
            vapidKey: 'BO50uWHMmyJY-TaKTpprGOsMIQqRKrYRKOkikvshO99HYaYnxnRokDlPSMMGyoAGUfL-yXKdDfqMAlzeymLEdXw'
        });
        console.log('FCM Token:', token);

        await fetch('/save-fcm-token', {
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

// 4️⃣ Foreground messages
messaging.onMessage((payload) => {
    console.log('Message received in foreground:', payload);
    // 🔥 SHOW NOTIFICATION MANUALLY
    // new Notification(payload.notification.title, {
    //     body: payload.notification.body,
    //     icon: '/images/favicon.png' // optional
    // });
    navigator.serviceWorker.getRegistration().then(function(registration) {
        if (registration) {
            registration.showNotification(payload.notification.title, {
                body: payload.notification.body,
                icon: '/images/favicon.png'
            });
        }
    });
});