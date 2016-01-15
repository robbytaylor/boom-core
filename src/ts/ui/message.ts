export class Message {
	private $document = $(top.document);

	constructor(message: string) {
		this.open(message);
	}

	open(message: string) {
		declare var Notification;

		var notified = false,
			waitingApproval = false,
			timer,
			notification = this;

		if ("Notification" in window && Notification.permission !== 'denied') {
			waitingApproval = true;

			Notification.requestPermission(function (permission) {
				waitingApproval = false;

				if (permission === "granted") {
					var n = new Notification('BoomCMS', {
						body: message,
						icon: '/vendor/boomcms/boom-core/img/logo-sq.png',
						requireInteraction: false
					});

					notified = true;

					setTimeout(function() {
						n.close();
					}, 3000);
				}
			});
		}

		timer = setInterval(function() {
			if ( ! waitingApproval && ! notified) {
				notification.showFallback(message);
				clearInterval(timer);
			}
		}, 100);
	}

	private showFallback(message: string) {
		$.jGrowl(message);
	};
};
