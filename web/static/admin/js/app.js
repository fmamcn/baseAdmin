layui.config({
    base: '/static/layui/extends/' // 配置 Layui 第三方扩展模块存放的基础目录
}).extend({
    notify: 'notify'
});
layui.use(['notify'], function(){
    var $ = layui.$;
    var $ = layui.$;
    var tabs = layui.tabs;
    var dropdown = layui.dropdown;
    var form = layui.form;
    var layer = layui.layer;
    var notify = layui.notify;
    var table = layui.table;

    // 弹窗最大高度
    var layerMaxHeight = 0;
    if (window.screen.height > 1440) {
        layerMaxHeight = 1000;
    } else if (window.screen.height <= 1440 && window.screen.height > 1080) {
        layerMaxHeight = 800;
    } else if (window.screen.height <= 1080 && window.screen.height > 900) {
        layerMaxHeight = 650;
    } else if (window.screen.height <= 900 && window.screen.height > 768) {
        layerMaxHeight = 520;
    } else if (window.screen.height <= 768) {
        layerMaxHeight = 420;
    }

    iframeHeight();

    $('.sidebar-0').show();
    $('.sidebar-0 li div[data-id=7]').addClass('layui-menu-item-checked').parent().addClass('layui-menu-item-checked');
    $('.menu-first ul li').on('click', function(){
        $(this).find('a').addClass('active').parent().siblings().find('a').removeClass('active');
        var navID = $(this).data('id');
        $('.menu-second ul').hide();
        $('.sidebar-' + navID).show();
    });  

    // 为标签头添加右键菜单
    var dropdownInst = dropdown.render({
        elem: '#headerTabs .layui-tabs-header>li',
        trigger: 'contextmenu',
        data: [{
            title: '关闭',
            action: 'close',
            mode: 'this',
        }, {
            title: '关闭其他标签页',
            action: 'close',
            mode: 'other'
        }, {
            title: '关闭所有标签页',
            action: 'close',
            mode: 'all'
        }],
        click: function(data, othis, event) {
            var index = this.elem.index();
            // 新增标签操作
            if (data.mode === 'this') {
                tabs.close('headerTabs', index); // 关闭当前标签
            } else {                
                tabs.closeMult('headerTabs', data.mode, index); // 批量关闭标签
                if (index == 0) {
                    tabs.change('headerTabs', 'TAB7', true);
                } else {
                    tabs.change('headerTabs', index, true);
                }
                
            }
        }
    });  

    $('.menu-second ul li').click(function(){
        var _this = $(this).find('.layui-menu-body-title');
        var url = _this.data('url');
        var title = _this.text();
        var id = _this.data('id');
        var tabsId = tabs.getHeaderItem('headerTabs', 'TAB' + id);
        if (tabsId.length) {
            // 如果有该标签则切到
            tabs.change('headerTabs', 'TAB' + id);
        } else {
            tabs.add('headerTabs', {
                title: title,
                id: 'TAB' + id,
                url: url,
                done: function(params) {                                   
                    dropdown.render($.extend({}, dropdownInst.config, {
                        elem: params.thisHeaderItem
                    }));
                    $('#iframe').attr('src', url); 
                }
            });            
        }        
    });


    tabs.on('afterChange(headerTabs)', function(data) {
        var tabID = this.getAttribute('lay-id');
        var url = this.getAttribute('lay-url');
        tabID = tabID.match(/TAB(.*)/)[1];
        $.get(atob(_U) + '?id=' + tabID, function(res){
            $('.menu-first ul li').find('a').removeClass('active');
            $('.menu-first ul li[data-id=' + res +']').click();
            $('.sidebar-' + res + ' li').siblings().removeClass('layui-menu-item-checked').find('div').removeClass('layui-menu-item-checked');
            $('.sidebar-' + res + ' li div[data-id=' + tabID + ']').addClass('layui-menu-item-checked').parent().addClass('layui-menu-item-checked');
        })
        $('#iframe').attr('src', url);
    });

    // 通用弹窗
    $('.T-modal').on('click', function(res){
        var widthData = $(this).data('width');
        var title = $(this).html();
        width = widthData ? widthData : 800;
        var url = $(this).data('href');
        modal = layer.open({
            type: 2,
            title: title,
            shade: [0.2, '#fff'],
            anim: 5,
            maxHeight: layerMaxHeight,
            isOutAnim: false,
            area: [width + 'px', 'auto'],
            content: url,
            success: function(layero, index, that) {
                layer.iframeAuto(index);
                that.offset();
            }
        });        
    });

    $('.T-update').on('blur', function () {
        var load = layer.load(2);
        var url = $(this).data('href');
        // 参数
        var data = {};
        data[$(this).attr('name')] = $(this).val();
        data['_verify'] = 0;

        ajaxPost(url, data, load);
        return false;
    });

    $(".T-del").on('click', function(){
        var url = $(this).data('href');
        layer.confirm('确认执行当前操作？', {icon: 3, title: false, closeBtn: 0}, function(index){
            layer.close(index);
            var load = layer.load(2);
            ajaxPost(url, [], load);
        });
    });

    // 通用批量
    $('.T-batch').on('click', function () {
        var url = $(this).data('href');
        var val = [];
        $('.layui-table tbody input[type=checkbox]:checked').each(function (i) {
            val[i] = $(this).val();
        });
        if (val === undefined || val.length == 0) {
            notify.warning('请选择数据项');
            return false;
        }
        layer.confirm('确认执行当前操作？', {icon: 3, title: false, closeBtn: 0}, function(index){
            layer.close(index);
            var load = layer.load(2);
            var data = {};
            data[$('.layui-table tbody input[type=checkbox]:checked').attr('name')] = val;
            data['_verify'] = 0;
            ajaxPost(url, data, load);
        });        
        return false;
    });

    $('.T-action').on('click', function () {
        var url = $(this).data('href');
        var load = layer.load(2);
        ajaxPost(url, [], load);
        return false;
    });

    function ajaxPost(url, data = [], load = 0){
        $.post(url, data, function(res){
            if (load) {
                layer.close(load);
            }            
            if (res.code === 1) {
                notify.success(res.msg, function () {
                    window.location.href = res.url
                });
            } else {
                notify.error(res.msg);
            }                
        })
    }

    // 全选
    form.on('checkbox(all)', function(data){
        if (data.elem.checked == true) {
            $(".layui-table tbody input[type=checkbox]").prop("checked", true);
            form.render('checkbox');
        } else {
            $(".layui-table tbody input[type=checkbox]").prop("checked", false);
            form.render('checkbox');
        }
    });

    $(window).resize(function(){
        iframeHeight();
    });

    function iframeHeight(){
        $('.app-iframe').height($(window).height() - 96);
    }

    form.on('submit(J-form)', function(data){
        $('button[type=submit]').addClass('layui-btn-disabled').attr('disabled', 'disabled');
        var index = parent.layer.getFrameIndex(window.name);
        var load = layer.load(2);
        $.post(data.form.action, data.field, function(res) {
            layer.close(load);
            if (res.code == 1) {
                notify.success(res.msg, function(){
                    if (index) {
                        parent.location.reload();                                                                       
                    } else {
                        window.location.href = res.url;
                    }
                })
            } else {
                msg = res.msg ? res.msg : '无修改';
                notify.error(msg);
                if ($('.captcha').length) {
                    var src = $('.captcha').attr('src');
                    $('.captcha').attr('src', src + '?rand=' + Math.random());
                }
                $('button[type=submit]').removeClass('layui-btn-disabled').removeAttr('disabled');
            }
        });
        return false;
    });

    table.on('tool(dataTable)', function(res){
        var data = res.data; // 获得当前行数据
        switch (res.event) {
            case 'edit':
                var title = $(this).html();
                var widthData = $(this).data('width');
                width = widthData ? widthData : 800;
                var url = $(this).data('href');
                //iframe层-父子操作
                modal = layer.open({
                    type: 2,
                    title: title,
                    shade: [0.2, '#fff'],
                    anim: 5,
                    maxHeight: layerMaxHeight,
                    isOutAnim: false,
                    area: [width + 'px', 'auto'],
                    content: url,
                    success: function(layero, index, that) {
                        layer.iframeAuto(index);
                        that.offset();
                    }
                });
                break;
            case 'del':
                var url = $(this).data('href');
                layer.confirm('确认执行当前操作？', {icon: 3, title: false, closeBtn: 0}, function(index){
                    layer.close(index);
                    var load = layer.load(2);
                    $.post(url + '?id=' + data.id, function(res){
                        layer.close(load);
                        if (res.code === 1) {
                            notify.success(res.msg, function () {
                                table.reload('dataTable')
                            });
                        } else {
                            notify.error(res.msg);
                        }
                    })
                });
                break;
            case 'info':
                var url = $(this).data('href');
                var widthData = $(this).data('width');
                var title = $(this).html();
                width = widthData ? widthData : 800;
                //iframe层-父子操作
                modal = layer.open({
                    type: 2,
                    title: title,
                    anim: 'slideLeft',
                    shade: [0.2, '#000'],
                    offset: 'r',
                    isOutAnim: false,
                    shadeClose: true,
                    move: false,
                    scrollbar: false,
                    area: [width + 'px', '100%'],
                    content: url
                });
                break;
            case 'add':
                var url = $(this).data('href');
                var title = $(this).html();
                var widthData = $(this).data('width');
                width = widthData ? widthData : 800;
                //iframe层-父子操作
                modal = layer.open({
                    type: 2,
                    title: title,
                    shade: [0.2, '#fff'],
                    anim: 5,
                    maxHeight: layerMaxHeight,
                    isOutAnim: false,
                    area: [width + 'px', 'auto'],
                    content: url,
                    success: function(layero, index, that) {
                        layer.iframeAuto(index);
                        that.offset();
                    }
                });
                break;
        }
    });
});