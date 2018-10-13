<!DOCTYPE html>
<html lang="en">

<?php include 'header.php';?>

<style type="text/css">

* {
  box-sizing: border-box;
}
body {
  font: 16px Arial;  
}
.autocomplete {
  /*the container must be positioned relative:*/
  position: relative;
  display: inline-block;
}
input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}
input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}
input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}
.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}
.autocomplete-items div:hover {
  /*when hovering an item:*/
  background-color: #e9e9e9; 
}
.autocomplete-active {
  /*when navigating through the items using the arrow keys:*/
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}


label {
  margin-right: 1rem;
}

fieldset {
  border: none;
}

legend {
  font-weight: bold;
}
		
</style>

<body class="fix-header fix-sidebar">

	
	    <div id="main-wrapper">
        <!-- header header  -->
        <div class="header">
			<?php include 'nav.php';?>
        </div>
        <!-- End header header -->
			<?php include 'leftside.php';?>
        <!-- Page wrapper  -->
        <div class="page-wrapper">
            <!-- Bread crumb -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-primary">Advanced</h3> </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
            <!-- End Bread crumb -->
            <!-- Container fluid  -->
            <div class="container-fluid">
			
                <!-- Start Page Content -->
                <div class="row">
				<div class="col-md-6">
				<span class="input-group-btn"><button class="btn btn-primary" type="submit"><i class="ti-search"></i></button></span>
				  <div class="autocomplete">
					<input id="myInput" type="text" name="myCountry" placeholder="Search Display Name">
				  </div>
				  </div> 
                </div>

                <div class="row bg-white m-l-0 m-r-0 box-shadow ">
				<!--<button id="update">Test</button>-->
                    <!-- column -->
                    <div class="col-lg-12">
						<svg class='chart' width="100%" height="400"/>
						<div class="controls"></div>
                    </div>
                    <!-- column -->
                </div>
 
                <!-- End PAge Content -->
            </div>
		
            <!-- End Container fluid  -->
            <!-- footer -->
			<?php include 'footer.php';?>
            <!-- End footer -->
        </div>
        <!-- End Page wrapper  -->
    </div>
<?php include 'footerScripts.php';?>

<script src="//d3js.org/d3.v4.min.js"></script>
<script src="//cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js" charset="utf-8"></script>
<script src=".script-compiled.js"></script>
<script>
    // change frame height
    //d3.select(self.frameElement).style('height', '660px');
</script>

<script>

function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener("input", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement("DIV");
      a.setAttribute("id", this.id + "autocomplete-list");
      a.setAttribute("class", "autocomplete-items");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement("DIV");
          /*make the matching letters bold:*/
          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener("click", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName("input")[0].value;
			  displayUserProfile(inp.value);
			  console.log(inp.value);
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener("keydown", function(e) {
      var x = document.getElementById(this.id + "autocomplete-list");
      if (x) x = x.getElementsByTagName("div");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the "active" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as "active":*/
    if (!x) return false;
    /*start by removing the "active" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class "autocomplete-active":*/
    x[currentFocus].classList.add("autocomplete-active");
  }
  function removeActive(x) {
    /*a function to remove the "active" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove("autocomplete-active");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName("autocomplete-items");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  
}


window.onload = function() {
$('.col-lg-12').width();
	console.log("Test!");
   var dataString = 'test';
   $.ajax({
       type:'POST',
       data:dataString,
       url:'get_users.php',
	   dataType: 'json',
       success:function(data) {
		//console.log(data);
		var users=[];
		for (var i = 0; i < data.length; i++) {
            users[i]=data[i];
        }
		//console.log(questions);
		autocomplete(document.getElementById("myInput"), users);
		}
	});
	
	var dataString = 'displayName='+"dingo409";
	   $.ajax({
       type:'POST',
       data:dataString,
       url:'get_user_profile.php',
	   dataType: 'json',
       success:function(data) {
		console.log("Data: "+data);
		displayChart(data);
		//var users=[];
		//for (var i = 0; i < data.length; i++) {
        //    users[i]=data[i];
        //}
		//console.log(questions);
		
		
		
		}
	});
	
};



function createChart (svg, data) {

  const colors = ['#98abc5', '#d0743c', '#6b486b']

  svg = d3.select(svg)
  const margin = {top: 20, right: 90, bottom: 40, left: 40}
  const width = $('svg').width() - margin.left - margin.right
  const height = $('svg').height() - margin.top - margin.bottom
  const g = svg.append('g').attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')

  var x0 = d3.scaleBand()
    .rangeRound([0, width])
    .paddingInner(0.1)

  var x1 = d3.scaleBand()
    .padding(0.05)

  var y = d3.scaleLinear()
    .rangeRound([height, 0])

  var z = d3.scaleOrdinal()
    .range(colors)

  // check each subset of data for possible sections, since not all subsets have every possible section.
  let nameKeys = data[Object.keys(data)[0]].map(obj =>obj.name)
  let valueKeys =   ["#ofHypothesis", "#ofCommDesc", "#TotalPosts"]

    //fill in empty data entries
    Object.keys(data).forEach((d)=>{
      data[d].forEach(section=>{
        valueKeys.forEach(k=>{
          if (section.values[k] === undefined) section.values[k] = 0
        })
      })
    })

  x0.domain(nameKeys)
  x1.domain(valueKeys).rangeRound([0, x0.bandwidth()])

  const barContainer = g.append('g')

  const xAxis = g.append('g')
      .attr('class', 'axis')
      .attr('transform', 'translate(0,' + height + ')')
      .call(d3.axisBottom(x0))

  const yAxis = g.append('g')
      .attr('class', 'axis')

  yAxis
    .append('text')
      .attr('x', 2)
      .attr('y', y(y.ticks().pop()) + 0.5)
      .attr('dy', '0.32em')
      .attr('font-weight', 'bold')
      .attr('text-anchor', 'start')
      .text('Prop Value')

  var legend = g.append('g')
  .attr('font-size', 10)
  .attr('text-anchor', 'end')
  .attr("transform", function(d, i) { return "translate(90," +  20 + ")"; })

  legend.append('text')
  .text('Legend')
  .attr('x', width - 19)
  .style('font-weight', 'bold')
  .attr('dy', -10)
  .attr('dx', 20)

  var legendEnter = legend
    .selectAll('g')
    .data(valueKeys)
    .enter().append('g')
      .attr('transform', function (d, i) { return 'translate(0,' + i * 20 + ')' })

  legendEnter.append('rect')
      .attr('x', width - 19)
      .attr('width', 19)
      .attr('height', 19)
      .attr('fill', z)

  legendEnter.append('text')
      .attr('x', width - 24)
      .attr('y', 9.5)
      .attr('dy', '0.32em')
      .text(d => d)

  const stack = d3.stack()
      .keys(valueKeys)

  // updates both the year + the chart type (group or stacked)
  function updateChart (data, chartType='group') {

    // ========================================================
    //  show grouped view
    // ========================================================

    if (chartType === 'group'){

      //find max value of a section
      const maxValue = d3.max(data.map((d) => Object.values(d.values)).reduce((a, b) => a.concat(b), []))
      y.domain([0, maxValue]).nice()

      yAxis.transition()
      .call(d3.axisLeft(y))

      const barsWithData = barContainer
      .selectAll('g')
      .data(data)

      barsWithData.exit().remove()

      const bars = barsWithData
      .enter()
      .append('g')
      .attr('transform', function (d) { return 'translate(' + x0(d.name) + ',0)' })
      .merge(barsWithData)
      .selectAll('rect')
      .data(function (d) {
        return Object.keys(d.values).map(k => ({ key: k, value: d.values[k] }))
      })

      bars.exit().transition().style('opacity', 0).remove()

      bars
      .enter()
      .append('rect')
      .attr('fill', function (d) {
        return z(d.key)
      })
      // start y at height (0) so animation in looks like bars are growing upwards
      .attr('y', height)
      .merge(bars)
      .transition()
      .attr('width', x1.bandwidth())
      .attr('x', function (d) { return x1(d.key) })
      .attr('y', d => y(d.value))
      .attr('height', d => height - y(d.value))

    }

    // ========================================================
    //  show stacked view
    // ========================================================
    else if (chartType === 'stack'){

      //find max value of a section
      const maxValue = d3.max(
        data.map((d) => Object.values(d.values))
      .map((valueArray)=>{
        return valueArray.reduce((a,b)=> a+ b)
      })
    )

      y.domain([0, maxValue]).nice()

      yAxis.transition()
      .call(d3.axisLeft(y))

      //add data for missing bars
      const seriesFlipped = stack(data.map(d=>{
        const defaultData = {}
        valueKeys.forEach(k=> defaultData[k] = 0)
      return Object.assign(defaultData, d.values)
      }))

      const series = []
      //need to reorient the series
      //we want a list of groups, not a list of rects from each level
      seriesFlipped[0].forEach((col, i)=>{
        const arr = []
        seriesFlipped.forEach((row, index2)=>{
          //mimic the key from the grouped data format
          row[i].key = index2 + 1 + ''
          arr.push(row[i])
        })
        series.push(arr)
      })

      const barSections = barContainer
      .selectAll('g')
      .data(series)

      const bars = barSections
      .enter()
      .append('g')
      .merge(barSections)
      .attr('transform', (d,i)=> {console.log(x0(nameKeys[i])); return 'translate(' + x0(nameKeys[i]) + ',0)'} )
      .selectAll('rect')
      .data(d=>d, (d)=> d.key)

      const enterBars = bars.enter().append('rect')
      .attr('fill',  (d)=> z(d.key))
      bars.exit().transition().style('opacity', 0).remove()

      enterBars
      .merge(bars)
      .transition()
      .delay((d,i)=> i * 50)
      .attr('width', x0.bandwidth())
      .attr("y", function(d) {return y(d[1]) })
      .attr("x", 0)
      .attr("height", function(d) { return y(d[0]) - y(d[1]) })

    }

  }


  return {
    updateChart
  }
}


const globalChart=null;

function displayChart(data)
{
   //d3.select("svg").remove();	
	//start with the first year selected
  const chart = createChart(document.querySelector('svg'), data)  
  // append the input controls

  const fieldset2 = d3.select('.controls').append('fieldset')
  const types =  ['group', 'stack']
  fieldset2.append('legend').text('Graph Layout')

  types.forEach((graphType, index)=>{
    const label = fieldset2.append('label')
    label.append('input')
    .attr('type', 'radio')
    .attr('name', 'graphType')
    .attr('value', graphType)
    .attr('checked', function(){
      if (index === 0) return true
      return null
    })
    .on('click', ()=>{
      chart.updateChart(data[Object.keys(data)[0]], graphType)
    })

    label.append('span')
    .text(graphType)

  })

  // render initial chart
  //console.log(data[Object.keys(data)[0]]);
  chart.updateChart(data[Object.keys(data)[0]])

}

var globalData;
var chart;

function displayUserProfile(displayName)
{
	 var dataString = 'displayName='+displayName;
	   $.ajax({
       type:'POST',
       data:dataString,
       url:'get_user_profile.php',
	   dataType: 'json',
       success:function(data) {
		 displayChart(data);
		}
	});
}

</script>


</body>
</html>
