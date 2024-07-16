$(document).ready(function(){
    $(document).on('click', '.load-more-items', function(){
        var targetContainer = $('[data-elements]'),
            url =  $('.load-more-items').attr('data-url');
        if (url !== undefined) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function(data){

                    $('.load-more-items').remove();

                    var elements = $(data).find('[data-elements]'),
                        pagination = $(data).find('.load-more-items');

                    targetContainer.append(elements);
                   $('#pagination').append(pagination);

                }
            });
        }
    });
});