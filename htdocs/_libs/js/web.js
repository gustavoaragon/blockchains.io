var blockchains = {
    // INITIALIZE
    init: function()
    {
        blockchains.nav();
        blockchains.qr();
        $('body').on('click', '.ga-track', function(e)
        {
            e.preventDefault();
            var href = $(this).attr('href');
            var action = $(this).attr('data-action');
            var place = $(this).attr('data-place');
            blockchains.track(e, href, action, place);
        });
    },
    nav: function()
    {
        $('.dropdown-menu').on('click', function(event) {
            if($(this).find('select').length > 0)
            {
                event.stopPropagation();
            }
        });
    },
    modal: function(title, content, id)
    {
        var selector = $('#default-modal');
        if(id) selector = $('#'+id);
        if(title) $(selector).find('.modal-title').html(title);
        if(content) $(selector).find('.modal-body').html(content);
        $(selector).modal('show');
    },
    qr: function()
    {
        $('body').on('click', '.btn-qr', function(e)
        {
            e.preventDefault();
            var content = $(this).attr('data-content');
            var qr = '<p class="qr" data-content="'+content+'"></p>';
            blockchains.modal(content, '<div class="qr-holder">'+qr+'</div>');
        });
        $('.modal').on('show.bs.modal', function(e)
        {
            $(this).find('p.qr').each(function()
            {
                if($(this).find('img').length > 0)
                {
                    $(this).find('img').remove();
                }
                $(this).qrcode({
                    render: 'image',
                    text: $(this).attr('data-content')
                });
            });
        });
    },
    track: function(e, href, action, place)
    {
        e.preventDefault();
        _gaq.push(
            [
                '_trackEvent',
                action,
                place,
                href
            ]
        );
        setTimeout(function()
        {
            location.href = href;
        }, 200);
        return false;
    }
};

$(document).ready(function()
{
    blockchains.init();
});