var blockchains = {
    // INITIALIZE
    init: function()
    {
        blockchains.nav();
    },
    nav: function()
    {
        $('.dropdown-menu').on('click', function(event) {
            if($(this).find('select').length > 0)
            {
                event.stopPropagation();
            }
        });
    }
};

$(document).ready(function()
{
    blockchains.init();
});