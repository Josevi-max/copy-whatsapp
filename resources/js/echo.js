import Echo from 'laravel-echo';
 
import Pusher from 'pusher-js';
window.Pusher = Pusher;
 
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "49d4318c3d49a7ac475b",
    cluster: 'eu',
    forceTLS: true
});
