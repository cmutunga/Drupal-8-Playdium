
//this simple code tests that services yml works and will be replaced

var slider1 = document.getElementById("a");
var slider2 = document.getElementById("b");
var ans = document.getElementById("c");

var aBtn = document.getElementById('btn');

aBtn.addEventListener("click", function () {
    alert("I've been clicked");
    addNums();
})

function addNums() {
    var val1 = slider1.valueAsNumber;
    var val2 = slider2.valueAsNumber;
    ans.textContent = val1+ val2;
}

var aHeader = document.querySelector("h4");
aHeader.addEventListener("mouseover", function () {
        if (aHeader.style.color == "pink"){
            aHeader.style.color = "blue";
        } else {
            aHeader.style.color = "pink";
        }
    }
)