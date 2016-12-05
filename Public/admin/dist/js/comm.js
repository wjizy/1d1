
function load(isload){
    if(isload){
        var index = layer.load();
    }else{
        layer.closeAll();
    }
}
var request=function(data,url,func){
    $.ajax({
        type: 'post',
        data: data,
        dataType: 'json',
        url: url,
        beforeSend: load(true),
        success:function(data){
            func(data);
        },
        complete: load(false),
    })
}