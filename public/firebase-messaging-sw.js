// firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyD-LjO3dsAUkruYXEZU0l4BLS1LKzFXCyQ",
    authDomain: "fama-10735.firebaseapp.com",
    projectId: "fama-10735",
    storageBucket: "fama-10735.firebasestorage.app",
    messagingSenderId: "1014441330386",
    appId: "1:1014441330386:web:1e8c8da5343990e483cf0b",
});

const messaging = firebase.messaging();


// ✅ Handle notification click → open the link
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const link = event.notification.data?.link;
    if (link) {
        event.waitUntil(clients.openWindow(link));
    }
});