$(function(){
	$('.authorize-comment').click(function(event){event.preventDefault()});

	$("body").on("click",".send-comment",function(event){
		event.preventDefault();
		$that=$(this);
		data = $('.comments-add__form').serializeArray();
		data.push({'name':'eid','value':$('.comments-wrap').data('eid')});
		if($('.comments-show-all-items').length>0){
			data.push({'name':'comments_count','value':$('.comments-show-all-items').data('curpsize')});
		}
		data.push({'name':'hlblock_submit','value':'true'});
		
		$.ajax({
			url:'/ajax/comments.php',
			dataType:'html',
			method:'post',
			data:data,
			success:function(data){
				$('.comments-add__input').val('');
				$('.comments-list').html(data);
			}
			
		});
	});
});