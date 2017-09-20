function isPrime(num){
	if(num % 2 == 0 && num != 2) return false;
	for(var i = 3; i * i <= num; i += 2){
		if(num % i == 0) return false;
	}
	return true;
}
// ↑素数かどうかを判定する関数はたったの7行です。ね、簡単でしょう？

function PrimeFactorizationNotation(num){
	var ret = "";
	if(num % 2 == 0){
		var count = 0;
		while(num % 2 == 0){
			num /= 2;
			count++;
		}
		ret += 2;
		if(count != 1){
			ret += " ^ " + count;
		}
	}
	for(var i = 3; i <= num; i += 2){
		if(num % i == 0){
			var count = 0;
			while(num % i == 0){
				num /= i;
				count++;
			}
			if(ret != ""){
				ret += " x ";
			}
			if(count == 1){
				ret += i;
			}else{
				ret += i + " ^ " + count;
			}
			i = 3;
		}
	}
	return ret;
}

var targetNum; // 判定対象の数
var count_total = 0; // 回答数
var count_corret = 0; // 正答数
function onButtonClick_Judge(event, isYes){
	event.preventDefault();
	var result = ((isYes == isPrime(targetNum)) ? '○' : '×') + ': ';
	count_corret += (isYes == isPrime(targetNum)) ? 1 : 0;
	count_total++;
	if(isPrime(targetNum)){
		result += targetNum + " is a prime number.\n";
	}else{
		result += targetNum + " = " + PrimeFactorizationNotation(targetNum) + "\n";
	}
	UpdateNum();
	UpdateResult(result);
}

var objTextarea_Results;
var objSpan_Results;
function UpdateResult(result){
	objTextarea_Results.value = result + objTextarea_Results.value;
	objSpan_Results.innerHTML = "正答数/回答数 = " + count_corret + "/" + count_total + " = " + (Math.round((100 * count_corret / count_total) * 100) / 100) + "%";
}

var last = [1, 3, 7, 9, 11, 13];
function UpdateNum(){
	var rand1 = Math.floor(Math.random() * 13) + 1;
	var rand2 = Math.floor(Math.random() * 14);
	var rand3 = last[Math.floor(Math.random() * 6)];
	targetNum = Number(String(rand1) + String(rand2) + String(rand3));
	UpdateText();
}

function UpdateText(){
	document.getElementById('TargetNum').innerHTML = "「" + targetNum + "」は、<br>";
}

window.addEventListener('load', function(){
	targetNum = 1213;
	objTextarea_Results = document.getElementById("Textarea_Results");
	objTextarea_Results.value = "";
	objSpan_Results = document.getElementById("Span_Results");
	UpdateText();
});
