<!DOCTYPE HTML>
<html>
<head>
<title>title</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<script type="text/javascript">
(function(window, undefined){
	var tip = {
		closeBtn: true,
		tipNode: null,
		tipMain: null,
		tipHead: null,
		tipBody: null,
		speed: 'normal',
		timer: null,
		style: {display: 'none', position: 'absolute', zIndex: '99', color: '#FFFFFF', fontSize: '12px', width: '400px', height: '50px', left: '0px', top: '0px', wordWrap: 'break-word', overflow: 'hidden', border: '1px solid #000', opacity: 0, background: '#232323'},
		init: function(){
			tip.tipMain = addElement('div', 'tip-main');
			css(tip.tipMain, tip.style);
			tip.tipHead = addElement('div', 'tip-head', tip.tipMain);
			if(tip.closeBtn){
				var clsBtn = addElement('div', 'closeBtn', tip.tipHead);
				css(clsBtn, {display: 'block', margin: '0px', padding: '0px', width: '100%', textAlign: 'right', fontSize: '20px'});
				var lab = addElement('label', '', clsBtn);
				css(lab, {width: '20px', cursor: 'pointer'});
				lab.innerHTML = 'X';
				addEvent(lab, 'click', function(){
					tip.fadeOut(function(){
						css(tip.tipMain, {display: 'none'});
					});
				});
			}
			tip.tipBody = addElement('div', 'tip-body', tip.tipMain);
			tip.timer = new Timer({delay: tip.delay()});
		},
		create: function(id, msg){
			tip.tipNode = typeof(id) == 'object' ? id : $(id);
			css(tip.tipNode, {cursor: 'pointer'});
			tip.tipBody.innerHTML = msg;
			addEvent(tip.tipNode, 'mousemove', function(e){
				var size = getNodeSize(tip.tipNode);
				var offset = getOffset(tip.tipNode);
				css(tip.tipMain, {display: 'block', opacity: 0, left: offset.x + 6 + 'px', top: (size.y + offset.y + 10) + 'px'});
				tip.fadeIn();
			});
			addEvent(tip.tipNode, 'mouseout', function(e){
				tip.fadeOut(function(){
					css(tip.tipMain, {display: 'none'});
				});
			});
		},
		delay: function(){
			return tip.speed == 'fast' ? 40 : tip.speed == 'normal' ? 50 : 60;
		},
		fadeIn: function(){
			var opc = 0;
			tip.timer.start(function(){
				if(opc >= 1){
					tip.timer.stop();
				}
				css(tip.tipMain, {opacity: opc});
				opc = floatAdd(opc, 0.05);
			});
		},
		fadeOut: function(callback){
			var opc = 1;
			tip.timer.start(function(){
				if(opc < 0){
					tip.timer.stop();
					callback();
				}
				css(tip.tipMain, {opacity: opc});
				opc = floatSub(opc, 0.05);
			});
		}
	};
	
	var Timer = function(p){
		var delay = p.delay || 500;
		var intervalId = [];
		this.start = function(callback){
			var interId = setInterval(function(){
				callback();
			}, this.delay);
			intervalId.push(interId);
		};
		this.stop = function(){
			for(var i = 0; i< intervalId.length; i ++){
				clearInterval(intervalId[i]);
			}
		};
	};
	
	function isFunction(func){
		return typeof(func) == 'function' ? true : false;
	}
	
	function getOffset(node){
		var left = 0;
		var top  = 0;
		var offsetParent = node;
		while(offsetParent != null && offsetParent != document.body){
			//1: border width
			left += offsetParent.scrollLeft + 1;
			top  += offsetParent.scrollTop + 1;
			offsetParent = offsetParent.parentNode;
		}
		return {x: left, y: top};
	}
	
	function getNodeSize(node){
		node = node || (document.compatMode == 'BackCompat' ? document.documentElement : document.body);
		return {x: node.scrollWidth || node.clientWidth,
		        y: node.scrollHeight || node.clientHeight};
	}
	
	function isMouseLeaveOrEnter(e, handler){
		if(e.type != 'mouseout' && e.type != 'mousemove'){
			return false;
		}
		var reltag = e.relatedTag ? e.relatedTag : e.type == 'mouseout' ? e.toElement : e.fromElement;
		while(reltag && reltag != handler){
			reltag = reltag.parentNode;
		}
		return (reltag != handler);
	}
	
	function addEvent(node, event, listener){
		if(node.addEventListener){
			node.addEventListener(event, listener, true);
			return true;
		}else if(node.attachEvent){
			node.attachEvent('on' + event, listener);
			return true;
		}
		return false;
	}
	
	function addElement(tag, id, parrent){
		p = parrent || document.body;
		var node = document.createElement(tag);
		id && node.setAttribute('id', id);
		p.appendChild(node);
		return node;
	}
	
	function $(str, p){
		p = p || document;
		switch(str.charAt(0)){
			case '#':
				return p.getElementById(str.slice(1, str.length));
			break;
			case '.':
				var els = document.getElementsByTagName('*') || document.all;
				var len = els.length;
				var res = [];
				var str = str.slice(1, str.length).replace(/\-/g, '\\-');
				var reg = new RegExp('(^|\\s)' + str + '(\\s|$)', 'i');
				for(var i = 0; i < len; i ++){
					if(reg.test(els[i].className)){
						res.push(els[i]);
					}
				}
				return res;
			break;
			default:
				return p.getElementsByTagName(str);
			break;
		}
	}
	
	function css(e, style){
		if(typeof(e) == 'object'){
			e = [e];
		}else{
			e = $(e);
		}
		for(var i = 0; i < e.length; i ++){
			for (s in style){
				e[i].style[s] = style[s]; 
			}
		}
	}
	
	function camelize(s, sep){
		sep = sep || '-';
		return s.replace(/([a-z])([A-Z])/g, function(matchs, p1, p2){
			return p1 + sep + p2.toLowerCase();
		});
	}
	
	//修正js浮点数运算bug
	function floatAdd(a, b){
		var r1, r2, m;
		try{
			r1 = a.toString().split('.')[1].length;
		}catch(e){
			r1 = 0;
		}
		try{
			r2 = b.toString().split('.')[1].length;
		}catch(e){
			r2 = 0;
		}
		m = Math.pow(10, Math.max(r1, r2));
		return (a * m + b * m) / m;
	}
	
	function floatSub(a, b){
		var r1, r2, m, n;
		try{
			r1 = a.toString().split('.')[1].length;
		}catch(e){
			r1 = 0;
		}
		try{
			r2 = b.toString().split('.')[1].length;
		}catch(e){
			r2 = 0;
		}
		n = r1 > r2 ? r1 : r2;
		m = Math.pow(10, Math.max(r1, r2));
		return ((a * m - b * m) / m).toFixed(n);
	}
	
	//tip.init(); ie下报错
	//addEvent(window, 'load', function(){
//		tip.init();
//	});
	window.addEvent = addEvent;
	window.tip = tip;
})(window);

addEvent(window, 'load', function(){
	tip.init();
	tip.create('#demo', 'just for test');
});
</script>
</head>
<body>
<div id="demo" style="height:25px; border:1px solid #0C9;">Mouse Here.</div>
</body>
</html>