import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: '0476d68222f1be26cc88',
    cluster: 'ap1',
    forceTLS: true,
});

export default echo;
