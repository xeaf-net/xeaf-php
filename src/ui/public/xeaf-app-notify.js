/**
 * Клиент для подключения к XEAF Notification Service
 *
 * @author    Николай В. Анохин <n.anokhin@xeaf.net>
 * @copyright 2019 XEAF.NET Group
 *
 * @licence   Apache-2.0
 */
XNSClient = function () {

    /**
     * Имя Cookie с URL сервера XNS
     * @type {string}
     */
    this.URL = 'xns-url';

    /**
     * Имя Cookie с идентификатором сессии
     * @type {string}
     */
    this.SESSION_ID = 'xns-session-id';

    /**
     * Идентификатор нотификационного сообщения
     * @type {string}
     */
    this.NOTIFICATION_EVENT = '_NOTIFICATION';

    /**
     * Текст подтверждения получения сообщения
     * @type {string}
     */
    this.ACKNOWLEDGE_EVENT = 'OK';

    this.serviceURL = this.getCookie(this.URL);
    this.sessionId  = this.getCookie(this.SESSION_ID);
    this.socketIO   = null;
    this.errorState = false;

    let self = this;
    if (this.serviceURL !== null && this.sessionId !== null) {
        let url       = this.serviceURL + '?session=' + this.sessionId;
        this.socketIO = io(url, {
            autoConnect: false
        });
        this.socketIO.connect();
        this.socketIO.on('connect', function () {
            self.processConnect();
        });
        this.socketIO.on('error', function (event) {
            self.processError(event);
        });
        this.socketIO.on(this.NOTIFICATION_EVENT, function (event, callback) {
            if (!self.errorState) {
                callback(self.ACKNOWLEDGE_EVENT);
                self.processNotification(event);
            }
        });
    } else {
        console.log('You need a session to use notification service.');
    }
};

/**
 * Обработка успешного подключения
 */
XNSClient.prototype.processConnect = function () {
    console.log('Notification service connected!');
};

/**
 * Обработка ошибки подключения к серверу
 *
 * @param event Description of error event
 */
XNSClient.prototype.processError = function (event) {
    this.errorState = true;
    console.error('Error while connecting notification service.', event);
};

/**
 * Обработка входящего нотификационного сообщения
 *
 * @param event Notification data object
 */
XNSClient.prototype.processNotification = function (event) {
    console.log('notification: ', event);
};

/**
 * Возвращает значение Cookie
 *
 * @param {String} name Идентификатор
 *
 * @return {String}
 */
XNSClient.prototype.getCookie = function (name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return (match) ? unescape(match[2]) : null;
};
