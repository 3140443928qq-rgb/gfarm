jq(function(){
	jq('#friendname').val('');
	demo=function(event){
		var newX;
		var newY;
		if(jq('#goodtype').val()==9){
			newX = parseInt(event.pageX)-jq('#moveimg').width()/2;
			newY = parseInt(event.pageY)-jq('#moveimg').height()/1.3;
		}
		if(jq('#goodtype').val()==19){
			newX = parseInt(event.pageX)-jq('#moveimg').width()/2;
			newY = parseInt(event.pageY)-jq('#moveimg').height()/1.3;
		}
		if(jq('#goodtype').val()<0){
			newX = parseInt(event.pageX)-jq('#moveimg').width()/30;
			newY = parseInt(event.pageY)-jq('#moveimg').height()/2;
		}
		jq('#moveimg').css("left",newX + "px").css("top",newY + "px");	
	}
	jq('#goodtype').val('');
	jq('#maindiv').bind('mousemove',function(event){//图片随鼠标移动
		demo(event);
	});
	jq('#maindiv').bind("contextmenu",function(e){  //关闭右键功能
		return false;   
	});
	jq('#maindiv').mousedown(function(event){	//判断是否右击
		if(event.which==3){
			jq('#moveimg').hide();
			jq('#maindiv').css('cursor','auto');
		}
	});
	changeimg=function(obj){		//显示移动图片
		if(jq(obj).attr('tid')=='9'){
			jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/germ1.png').show();
		}else{
			jq('#moveimg').attr('src',jq(obj).attr('src')).show();
		}
		jq('#maindiv').css('cursor','none');
		jq('#dataid').val(jq(obj).attr('dataid'));
		jq('#goodtype').val(jq(obj).attr('tid'));	
	}
	jq('#ptable .makeland').each(function(){
		var tar=jq(this).next().attr('id');
		var _tar='_'+jq(this).next().attr('id');
		jq(this).next().find('.jiesuoimg').click(function(){
			if(jq('#hasuser').val()=='1'){
				if(jq(this).parent().prev().attr("_id")==undefined&&jq('#moveimg').css('display')=='none'){//解锁
					window.hideWindow('gfarm');
					window.hideWindow('gfarm1');
					if(jq('#jiesuoval').val()=='1'){
						window.showDialog(jq('#mjs').val(), 'confirm', '', function() {ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&formhash='+jq('#formhash').val()+'&act=landajax&landid='+tar,'');});
					}else{
						ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&formhash='+jq('#formhash').val()+'&act=landajax&landid='+tar,'');
					}
					
				}	
			}			
		});
		this.onclick=function(){			
			if(jq('#hasuser').val()=='1'){
				if(jq('#goodtype').val()=='9'&&jq(this).attr("_id")){//种植
					if(jq(this).next().next().children('img').length!=1&&jq('#moveimg').css('display')!='none'){					
						ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&depotid='+jq('#dataid').val()+'&formhash='+jq('#formhash').val()+'&act=germajax&landid='+tar,'');
					}
				}
				if(jq('#goodtype').val()=='-1'&&jq('#moveimg').css('display')!='none'&&jq(this).attr("_id")){//铲除作物
					var tarhtml=jq('#'+_tar).html();
					if(jq(this).next().next().children('img').length==1){	
						jq('#maindiv').unbind('mousemove');
						jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/sholve1.png');
						setTimeout(function(){jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/sholve.png');window.showDialog(jq('#mcc').val(), 'confirm', '', function() {ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&formhash='+jq('#formhash').val()+'&act=delete&landid='+tar,'');jq('#maindiv').bind('mousemove',function(event){
							demo(event);
						});},0,function(){jq('#maindiv').bind('mousemove',function(event){
							demo(event);
						});});	},100);							
					}
				}
				if(jq('#goodtype').val()=='-2'&&jq('#moveimg').css('display')!='none'&&jq(this).next().next().children('img').attr('id')=='cropimg'){//收获
					jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/hand1.png');
					setTimeout(function(){jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/hand.png')},100);					
					ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&formhash='+jq('#formhash').val()+'&act=getcrop&landid='+tar,'');
				}
			}		
			if(jq('#goodtype').val()=='19'&&jq(this).attr("_id")){//道具			
				if(jq(this).next().next().children('img').length==1&&jq(this).next().next().children('img').attr('id')!='cropimg'&&jq('#moveimg').css('display')!='none'){					
					ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&depotid='+jq('#dataid').val()+'&formhash='+jq('#formhash').val()+'&act=itemajax&landid='+tar+'&uid='+jq('#userid').val(),'');
				}
			}
			if(jq('#goodtype').val()=='-3'&&jq('#moveimg').css('display')!='none'&&jq(this).next().next().children('img').attr('id')=='cropimg'){//偷窃
				jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/hand1.png');
				setTimeout(function(){jq('#moveimg').attr('src','source/plugin/gfarm/img/ui/hand.png')},100);	
				ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&formhash='+jq('#formhash').val()+'&uid='+jq('#userid').val()+'&act=stealcrop&landid='+tar,'');
			}				
		}	
		jq(this).hover(function(){	
			jq(this).parent().children('.titlediv').show();
			//ajaxget('plugin.php?id=gfarm:front&mod=gfarm_ajax&act=seedetail&landid='+tar,'title_'+tar);		
		},function(){
			jq(this).parent().children('.titlediv').hide();
		});
	});
	jq('#rightcenter .clickfriend').click(function(){	//弹框好友
		if(jq('#friendflag').val()!='1'){
			jq('#rightcenter').animate({width:'236px'});
			jq('#friendflag').val('1');
		}else{
			jq('#rightcenter').animate({width:'30px'});
			jq('#friendflag').val('0');
		}
	});
	jq('#closemenu').click(function(){
		jq('#closemenu').hide();
		jq('#itemsul').hide();
	});
	jq('#bottomcenter span').each(function(){
		jq(this).click(function(){
			window.hideWindow('gfarm');
			window.hideWindow('gfarm1');
		});
	});
	jq('#refresh').click(function(){
		ajaxget('plugin.php?id=gfarm:front&mod=index&act=refresh&uid='+jq('#userid').val(),'ptable');	
	});
	jq('#sortmenu span').each(function(){
		jq(this).click(function(){
			jq(this).css('background','#fff2d7').find('a').css('color','#897654');
			jq(this).siblings().css('background','#3b1804').find('a').css('color','#a63900');
		});
	});
});