import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});


window.Echo.channel('users')
    .listen('.user.added', (e) => {
        console.log('User added:', e);
    });

window.Echo.channel('telegram-messages')
    .listen('.MessageReceived', (e) => {
        console.log('Message received:', e);
    });