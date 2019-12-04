/**
 * XEAF Application
 * @constructor
 */
XEAFApp = function () {
    this.portalURL     = this.getMetaContent('x-portal-url');
    this.secutityToken = 'x-auth-token';
    let options        = {autoHide: true, delay: 5000};
    $('.toast').toast(options);
    if (this.portalURL === undefined) {
        console.error('You must setup "portal-url" meta tag!');
    }
};

/**
 * Возвращает значение Cookie
 * @param name
 */
XEAFApp.prototype.getCookie = function (name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return (match) ? unescape(match[2]) : null;
};

/**
 * Возвращает значение контента тега META
 * @param name
 */
XEAFApp.prototype.getMetaContent = function (name) {
    return $('meta[name="' + name + '"]').attr('content');
};

/**
 * Устанавливает фокус на элемент формы
 * @param id
 */
XEAFApp.prototype.setElementFocus = function (id) {
    setTimeout(function () {
        $('#' + id).trigger('focus');
    }, 500);
};

/**
 * Отображает всплывающее сообщение
 */
XEAFApp.prototype.showToast = function (alert, title, message) {
    let toast = $('.toast');
    if (toast !== undefined) {
        let toastTitle = toast.children('.toast-header');
        let toastBody  = toast.children('.toast-body');
        toast.attr('class', 'toast bg-' + alert);
        toastTitle.attr('class', 'toast-header text-white bg-' + alert);
        toastTitle.children('strong').html(title);
        toastBody.html(message);
        toast.toast('show');
    }
};

/**
 * Подготавливает форму
 *
 * @param formId
 * @param successCallback
 * @param errorCallback
 */
XEAFApp.prototype.prepareForm = function (formId, successCallback, errorCallback) {
    let self   = this;
    let fullId = '#' + formId;
    $(document).off('submit', fullId);
    $(document).on('submit', fullId, function (e) {
        e.preventDefault();
        self.processAjaxPost(formId, successCallback, errorCallback);
        return false;
    });
    $(fullId).off('change', '.form-control');
    $(fullId).on('change', '.form-control', function () {
        let el = $(this);
        el.removeClass('is-invalid');
    });
};

/**
 * Загружает диалоговое окно и подготавливает форму
 * @param url
 * @param formId
 * @param successCallback
 * @param errorCallback
 */
XEAFApp.prototype.loadModalForm = function (url, formId, successCallback, errorCallback) {
    let self = this;
    $('#' + formId).remove();
    $.ajax({
        type   : "GET",
        url    : url,
        data   : {},
        success: function (data) {
            $('body').append(data);
            let form = $('#' + formId).children('.modal');
            form.on('shown.bs.modal', function () {
                $(this).find('[autofocus]').trigger('focus');
            });
            form.modal('show');
            self.prepareForm(formId, successCallback, errorCallback);
            return false;
        },
        error  : function (error) {
            if (errorCallback !== undefined) {
                errorCallback(error);
            }
            // xeafApp.processAjaxError(error)
            self.processAjaxError(error)
        }
    });
};

/**
 * Закрывает диалоговое окно формы
 * @param formId
 */
XEAFApp.prototype.closeModalForm = function (formId) {
    $('#' + formId).children('.modal').modal('hide');
};

/**
 * Универсальный обработчик post submit'a формы
 *
 * @param formId          id формы
 * @param successCallback Дополнительные действия при success
 * @param errorCallback   Дополнительные действия при error
 *
 * 1. method  - post
 * 2. enctype - multipart/form-data
 * 3. action  - url
 */
XEAFApp.prototype.processAjaxPost = function (formId, successCallback, errorCallback) {

    let form   = document.querySelector('#' + formId);
    let data   = new FormData(form);
    let url    = form.action;
    let self   = this;
    let status = this.disableFormElements(formId);
    data.append(this.secutityToken, this.getCookie(this.secutityToken));

    $.ajax({
        type       : "POST",
        url        : url,
        data       : data,
        processData: false,
        contentType: false,
        success    : function (result) {
            self.restoreFormElements(status);
            self.processAjaxPostSuccess(result);
            if (successCallback !== undefined && successCallback != null) {
                successCallback(result);
            }
            if (result.alert === 'success') {
                self.closeModalForm(formId);
            }
            return false;
        },
        error      : function (error) {
            self.restoreFormElements(status);
            if (errorCallback !== undefined && errorCallback !== null) {
                errorCallback(error);
            }
            self.processAjaxError(error);
        }
    });
    return false;
};

/**
 * Универсальный обработчик успешной отправки post запроса
 * @param result
 */
XEAFApp.prototype.processAjaxPostSuccess = function (result) {
    let keys = Object.keys(result.objectErrors);
    if (Object.keys(result.objectErrors).length > 0) {
        this.setElementFocus(keys[0]);
        for (let field in result.objectErrors) {
            let elInp = $('#' + field);
            elInp.addClass('is-invalid');
            let elErr = elInp.nextAll('.invalid-feedback:first');
            if (elErr !== undefined) {
                elErr.html(result.objectErrors[field]);
            }
        }
    }
    if (result.message.length !== 0) {
        this.showToast(result.alert, result.title, result.message);
    }
    if (result.alert === 'success') {

    }
};

/**
 * Универсальный обработчик ошибок AJAX запросов
 * @param error
 */
XEAFApp.prototype.processAjaxError = function (error) {
    console.error(error);
    if (error.status === 401) {
        let json = error.responseJSON;
        if (json.alert !== undefined && json.message !== undefined && json.message.length > 0) {
            this.showToast(error.responseJSON.alert, error.responseJSON.title, error.responseJSON.message);
        }
        let url = this.portalURL;
        setTimeout(function () {
            window.location = url;
        }, 5000);
    }
};

/**
 * Запрещает кнопки на форме
 * @param formId
 * @return {Array}
 */
XEAFApp.prototype.disableFormElements = function (formId) {
    let result   = [];
    let elements = $('#' + formId).find('button,.form-control');
    for (let i = 0; i < elements.length; i++) {
        let item = {
            element : elements[i],
            disabled: elements[i].disabled
        };
        result.push(item);
        elements[i].disabled = true;
    }
    return result;
};

/**
 * Восстанавливает статус элементов на форме
 * @param elements
 */
XEAFApp.prototype.restoreFormElements = function (elements) {
    for (let i = 0; i < elements.length; i++) {
        elements[i].element.disabled = elements[i].disabled;
    }
};

/**
 * Возвращает ссылку на языковой ресурс компоненты DataTable
 */
XEAFApp.prototype.dataTableLanguage = function () {
//    return this.portalURL + '/public/datatables.lang';
    return this.portalURL + '/node_modules/datatables.net-plugins/i18n/Russian.lang';
};

/**
 * Инициализация компонента DataTable
 * @param api
 */
XEAFApp.prototype.dataTableInit = function (api) {
    let input        = $('.dataTables_filter input').off();
    let searchText   = '<i class="fa fa-search"></i>';
    let searchButton = $('<button class="btn btn-sm btn-primary btn-datatables-search ml-1">')
        .html(searchText).on('click', function () {
            api.search('' + input.val()).draw();
        });
    input.on('keypress', function (e) {
        if (e.key === 'Enter') {
            searchButton.trigger('click');
        }
    });
    input.on('keyup', function (e) {
        if (e.key === 'Escape') {
            input.val('');
            searchButton.trigger('click');
        }
    });
    $('.dataTables_filter').append(searchButton);
};

// noinspection JSUnusedGlobalSymbols
let xeafApp = new XEAFApp();
