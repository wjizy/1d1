var href=window.location.href;
var url = href.split('?')[0];
var con = [
    {
        'icons':'fa fa-fw fa-child',
        'name' : '用户',
        'menu' : [
            {
                'href'  :url+"?do=1",
                'icons' :'fa fa-circle-o',
                'name'  :'用户列表'
            }
        ],
    },
    {
        'icons':'fa fa-files-o',
        'name' : '新闻',
        'menu' : [
            {
                'href'  :url+"?do=2",
                'icons' :'fa fa-circle-o',
                'name'  :'新闻列表'
            },
            {
                'href'  :url+"?do=3",
                'icons' :'fa fa-circle-o',
                'name'  :'写新闻'
            }
        ],
    },
];
var leftside = function (config) {
    var str ='';
    for(var i in config){
        str+='<li class="treeview"><a href="#"><i class="'+config[i].icons+'"></i><span>'+config[i].name+'</span><span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i></span></a><ul class="treeview-menu">';
        for(var j in config[i].menu){
            str+='<li><a href="'+config[i].menu[j].href+'"><i class="'+config[i].menu[j].icons+'"></i>'+config[i].menu[j].name+'</a></li>';
        }
        str+='</ul></li>';
    }
    var left= document.getElementById('leftside');
    left.innerHTML=str;
}(con);