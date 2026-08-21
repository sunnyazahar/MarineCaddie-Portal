/**
 * Bootstrap-ish jQuery polyfills for modal / dropdown / collapse.
 */
function whenJqueryReady(callback) {
    if (typeof window.jQuery === 'function') {
        callback(window.jQuery);
        return;
    }
    var tries = 0;
    var timer = setInterval(function () {
        tries += 1;
        if (typeof window.jQuery === 'function') {
            clearInterval(timer);
            callback(window.jQuery);
            return;
        }
        if (tries >= 120) {
            clearInterval(timer);
        }
    }, 50);
}

function ensureBackdrop($) {
    var $backdrop = $('.modal-backdrop');
    if (!$backdrop.length) {
        $backdrop = $('<div class="modal-backdrop show"></div>')
            .on('click.mcCompatBackdrop', function () {
                $('.modal.show').last().modal('hide');
            })
            .appendTo(document.body);
    } else {
        $backdrop.addClass('show').show();
    }
    return $backdrop;
}

function removeBackdrop($) {
    $('.modal-backdrop').remove();
}

function showModal($, $modal) {
    $modal.removeClass('hidden').css('display', 'block').attr('aria-hidden', 'false').addClass('show');
    $('body').addClass('modal-open');
    ensureBackdrop($);
    $modal.trigger('show.bs.modal').trigger('shown.bs.modal');
}

function hideModal($, $modal) {
    $modal.removeClass('show').attr('aria-hidden', 'true').css('display', 'none').addClass('hidden');
    if (!$('.modal.show').length) {
        $('body').removeClass('modal-open');
        removeBackdrop($);
    }
    $modal.trigger('hide.bs.modal').trigger('hidden.bs.modal');
}

whenJqueryReady(function ($) {
    $.fn.modal = function (action) {
        return this.each(function () {
            var $modal = $(this);
            var next = action;
            if (next === 'toggle' || next === undefined || next === null || typeof next === 'object') {
                next = $modal.hasClass('show') ? 'hide' : 'show';
            }
            if (next === 'show') showModal($, $modal);
            else if (next === 'hide') hideModal($, $modal);
        });
    };
    $.fn.modal.__mcCompat = true;

    if (document.documentElement.dataset.mcCompatBound === '1') {
        return;
    }
    document.documentElement.dataset.mcCompatBound = '1';

    $(document).on('click.mcCompat', '[data-dismiss="modal"]', function (e) {
        e.preventDefault();
        $(this).closest('.modal').modal('hide');
    });

    $(document).on('click.mcCompat', '[data-toggle="dropdown"]', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $toggle = $(this);
        var $parent = $toggle.closest('.dropdown, .btn-group, .dropup');
        var $menu = $parent.find('> .dropdown-menu').first();
        if (!$menu.length) $menu = $toggle.next('.dropdown-menu');
        if (!$menu.length) return;

        var willOpen = !$menu.hasClass('show');
        $('.dropdown-menu.show').not($menu).removeClass('show').css('display', '');
        $('.dropdown.show, .dropdown-primary.open, .btn-group.show').not($parent).removeClass('show open');

        if (willOpen) {
            $parent.addClass('show open');
            $menu.addClass('show').css('display', 'block');
        } else {
            $parent.removeClass('show open');
            $menu.removeClass('show').css('display', '');
        }
    });

    $(document).on('click.mcCompat', function () {
        $('.dropdown-menu.show').removeClass('show').css('display', '');
        $('.dropdown.show, .dropdown-primary.open, .btn-group.show').removeClass('show open');
    });

    $(document).on('click.mcCompat', '[data-toggle="collapse"]', function (e) {
        var target = $(this).attr('data-target') || $(this).attr('href');
        if (!target || target === '#') return;
        e.preventDefault();
        var $target = $(target);
        if (!$target.length) return;
        var isOpen = $target.hasClass('show') || $target.is(':visible');
        if (isOpen) $target.removeClass('show in').slideUp(150);
        else $target.addClass('show in').slideDown(150);
    });

    $(document).on('keydown.mcCompat', function (e) {
        if (e.key === 'Escape') {
            $('.modal.show').last().modal('hide');
        }
    });
});
