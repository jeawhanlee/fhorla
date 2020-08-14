$(function(){
   setTimeout(function(){
        $.post("async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../../async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../../../async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../../../../async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../../../../../async/feedback_sess.php", {unset_pro_sess : '1'})
        $.post("../../../../../../async/feedback_sess.php", {unset_pro_sess : '1'})
   },2000);
    
    
    $("a").click(function(event){
        var href = $(this).attr('href');
        if(href == '#'){
            event.preventDefault();   
        }
    })
})