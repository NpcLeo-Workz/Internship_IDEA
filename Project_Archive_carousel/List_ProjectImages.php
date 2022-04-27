<html>
    <script>
//-----------------------------------------------------------------------------------------------------------------//
//appending the css and js files from tiny-slider to the header bcs for some reason doesn't work without them there
//-----------------------------------------------------------------------------------------------------------------//

        var link = document.createElement('link');
        link.type = 'text/css';
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/tiny-slider.css';
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js';
        document.head.appendChild(link);
        document.head.appendChild(script);
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/tiny-slider.css" rel="stylesheet"/>
<?php
//-----------------------------------------------------------------------------------------------------------------//
//function to get all directories and subdirectories
//-----------------------------------------------------------------------------------------------------------------//

function getAllDirs($directory, $directory_seperator) {
  $dirs = array_map(function ($item) use ($directory_seperator) {
    return $item . $directory_seperator;
  }, array_filter(glob($directory . '*'), 'is_dir'));
  foreach ($dirs AS $dir) {
    $dirs = array_merge($dirs, getAllDirs($dir, $directory_seperator));
    
  }
  return $dirs;
}
//-----------------------------------------------------------------------------------------------------------------//
//returns a list of all png files found in the directorylist
//-----------------------------------------------------------------------------------------------------------------//

function getAllImgs($directory) {
  $resizedFilePath = array();
  foreach ($directory AS $dir) {
    foreach (glob($dir . '*.png') as $filename) {
        $filenameArray = explode('/',$filename);
        $check=explode('_',$filenameArray[5]);
        //echo($check[5]);
        
        if(strcmp($check[0],'Project')==0 && stripos($filenameArray[5], '-')== false){
            array_push($resizedFilePath, $filename);
           // echo ('TRUE');
        //echo "<script>console.log('Debug Objects: " . $check[0] . "' );</script>";
        }
        //echo ($filename);
        //echo "<br>";
    }
  }
  
  return $resizedFilePath;
}

//-----------------------------------------------------------------------------------------------------------------//
//execute the directory and picture search with the given directory and directoryseperator
//-----------------------------------------------------------------------------------------------------------------//

$directory = "./wp-content/uploads/";
$directory_seperator = "/";
$allimages = getAllImgs(getAllDirs($directory, $directory_seperator));

//echo($allimages[0]);
?>
<script>
//-----------------------------------------------------------------------------------------------------------------//
//gets the imagelist from server to client side(from php to javascript)
//-----------------------------------------------------------------------------------------------------------------//

  var Images = <?php echo json_encode($allimages); ?>;

</script>

<div class="tools">
    <input type="text" id="searchbar" style="text-transform:none">
    <button id="btn_searchbar">Search</button>
  </div>
<div class="tartist-tiny-slider-wrap">
  <div class="tartist-tiny-slider" data-chunksize="8">
  </div>
</div>

<script >
//-----------------------------------------------------------------------------------------------------------------//
// makes an img tag in html for each image with the given attributes and ads it to a link
// the link is added to a subdivision of the carousel
//-----------------------------------------------------------------------------------------------------------------//

  Images.forEach(element => {
  var elem = document.createElement('img');
  
  var elementpath = 'https://www.idea-europa.eu/1646923756060' + element.substring(1);
  //console.log(elementpath);
  elem.src = elementpath;
  var elementlink = document.createElement('a');
  elementlink.href= elementpath;
  elementlink.target= '_blank';
  elementlink.rel = 'noopener noreferrer';
  elementlink.appendChild(elem);
  var IDiv = document.createElement('div');
  IDiv.className = 'tartist-tiny-slider__item';
  IDiv.appendChild(elementlink);
  document.getElementsByClassName('tartist-tiny-slider')[0].appendChild(IDiv);
});

//-----------------------------------------------------------------------------------------------------------------//
// forEach function
//-----------------------------------------------------------------------------------------------------------------//

var forEach = function (array, callback, scope) {
  for (var i = 0; i < array.length; i++) {
      callback.call(scope, i, array[i]); // passes back stuff we need
  }
}

// select all slider parent div.tartist-tiny-slider

var sliders = document.querySelectorAll('.tartist-tiny-slider');

// chunk function to make groups for every slider's childrens

var chunk = function ( array, size ) {
  let arr = [];
  for ( let i = 0; i < array.length; i += size ) {
      let newSlicedArray = Array.prototype.slice.call( array, i, i + size );
      arr.push(newSlicedArray);
  }
  return arr;
}
//-----------------------------------------------------------------------------------------------------------------//
// applying foreach function to the sliders
//-----------------------------------------------------------------------------------------------------------------//

forEach(sliders, function (index, value) {

  //selecting childrens
  let childrens = value.querySelectorAll(".tartist-tiny-slider__item");

  //getting chunksize from the parent
  let chunkSize = value.dataset.chunksize;

  //making final arrays for the children with chunk size
  let final = chunk( childrens, parseInt(chunkSize) );

  //wrapping the chunks with div.wrap
  let newHTML = final.map( towrap => towrap.reduce( (acc, el) => (acc.appendChild(el),acc) , document.createElement('div') ) ).forEach( el => {
      el.className ="wrap";
      value.appendChild(el)
  })

//-----------------------------------------------------------------------------------------------------------------//
//initialize tiny slider  
//-----------------------------------------------------------------------------------------------------------------//

  let slider = tns({
      container: value,
      items: 1,
      slideBy: "page",
      autoplay: false,
      autoplayButtonOutput: false,
      loop: true,
      mouseDrag: false,
      arrowKeys: true,
      gutter: 20,
      navPosition: "bottom",
      nav: true,
  });

//-----------------------------------------------------------------------------------------------------------------//
// Creating of the reset button, adding it to the htmlpage and giving it the onclick reset function
//-----------------------------------------------------------------------------------------------------------------//

  var reset = document.createElement('button');
  reset.textContent = 'Reset';
  reset.id = 'btn_reset';
  document.getElementsByClassName('tools')[0].appendChild(reset);
  var resetbtn = document.getElementById("btn_reset") 
  resetbtn.addEventListener("click", resetfunc);
  function resetfunc(){
    document.getElementById("searchbar").value = '';
    btnClick();
  }
});
</script>

<script>
//-----------------------------------------------------------------------------------------------------------------//
//adding the keypress ENTER to execute the btnclick in the searchbar
//-----------------------------------------------------------------------------------------------------------------//

  var btn = document.getElementById("btn_searchbar");
  btn.addEventListener("click", btnClick);
  activeelem = document.getElementById("searchbar");
  activeelem.addEventListener("keypress", function(event) {
	 if (event.key === "Enter") {
    btnClick();
    //console.log("event added");
     }
  });

//-----------------------------------------------------------------------------------------------------------------//
//searchbutton function to filter the pictures based on the search terms
//-----------------------------------------------------------------------------------------------------------------//

  function btnClick() {
    //console.log("gets called")
      var search = document.getElementById("searchbar").value;
      //console.log(search);
      var namecontainssearch = false;
      var elementnames = [];

      //filters the imagelist based on the searched values
      Images.forEach(element => {
        elements = element.split("/");
        elementnames.push(elements[elements.length -1]);
        //console.log(elements[elements.length -1]);
      });

//-----------------------------------------------------------------------------------------------------------------//
//checks if the searchbar isnt empty and splits the value of the inputfield in case there are multiple words or numbers
//-----------------------------------------------------------------------------------------------------------------//

      if(search != ""){
        var searchelements = search.split(" ");
        var filteredImages =[];
        
        for(var i = 0; i < elementnames.length; i++){
          for(var x =0; x< searchelements.length; x++)
          {

		        //removes the need to be Uppercase sensitive by making both sides of the equation lowercase
		        //the result must include all given searchterms in order for it to show up in the list
            if(elementnames[i].toLowerCase().includes(searchelements[x].toLowerCase())){
              //console.log(elementnames[i]);
              //console.log(searchelements[x]);
              namecontainssearch = true;
            } else{
              namecontainssearch = false;
              break;
            }
          }; 
          if(namecontainssearch){
            filteredImages.push(Images[i]); 
          }
        };   
      }

//-----------------------------------------------------------------------------------------------------------------//
//empties the carousel and sets it's value to the carouselwrap and sets the data-chunksize to 8
//-----------------------------------------------------------------------------------------------------------------//

      document.getElementsByClassName('tartist-tiny-slider-wrap')[0].innerHTML = "<div class=\"tartist-tiny-slider\" data-chunksize=\"8\"></div>";

//-----------------------------------------------------------------------------------------------------------------//
//does the same as the initial carouselcreation but instead of the full image list it uses the filtered one
//checks if there are any images before attempting to fill the carousel
//-----------------------------------------------------------------------------------------------------------------//

      if(typeof filteredImages !== 'undefined' ){
        filteredImages.forEach(element => {
        var elem = document.createElement('img');
        
        var elementpath = 'https://www.idea-europa.eu/1646923756060' + element.substring(1);
        //console.log(elementpath);
        elem.src = elementpath;
        var elementlink = document.createElement('a');
        elementlink.href= elementpath;
        elementlink.target= '_blank';
        elementlink.rel = 'noopener noreferrer';
        elementlink.appendChild(elem);
        var IDiv = document.createElement('div');
        IDiv.className = 'tartist-tiny-slider__item';
        IDiv.appendChild(elementlink);
        document.getElementsByClassName('tartist-tiny-slider')[0].appendChild(IDiv);
        });
      } else{

        //if the searchbar was empty it will fill the list with all the images
        Images.forEach(element => {
        var elem = document.createElement('img');
        
        var elementpath = 'https://www.idea-europa.eu/1646923756060' + element.substring(1);
        //console.log(elementpath);
        elem.src = elementpath;
        var IDiv = document.createElement('div');
        IDiv.className = 'tartist-tiny-slider__item';
        IDiv.appendChild(elem);
        document.getElementsByClassName('tartist-tiny-slider')[0].appendChild(IDiv);
        });
      }

//-----------------------------------------------------------------------------------------------------------------//
// forEach function
//-----------------------------------------------------------------------------------------------------------------//

    var forEach = function (array, callback, scope) {
      for (var i = 0; i < array.length; i++) {
          callback.call(scope, i, array[i]); // passes back stuff we need
      }
    }

    // select all slider parent div.tartist-tiny-slider
    var sliders = document.querySelectorAll('.tartist-tiny-slider');

    // chunk function to make groups for every slider's childrens
    var chunk = function ( array, size ) {
      let arr = [];
      for ( let i = 0; i < array.length; i += size ) {
          let newSlicedArray = Array.prototype.slice.call( array, i, i + size );
          arr.push(newSlicedArray);
      }
      return arr;
    }

//-----------------------------------------------------------------------------------------------------------------//
// applying foreach function to the sliders
//-----------------------------------------------------------------------------------------------------------------//

    forEach(sliders, function (index, value) {

      //selecting childrens
      let childrens = value.querySelectorAll(".tartist-tiny-slider__item");

      //getting chunksize from the parent
      let chunkSize = value.dataset.chunksize;

      //making final arrays for the children with chunk size
      let final = chunk( childrens, parseInt(chunkSize) );

      //wrapping the chunks with div.wrap
      let newHTML = final.map( towrap => towrap.reduce( (acc, el) => (acc.appendChild(el),acc) , document.createElement('div') ) ).forEach( el => {
        el.className ="wrap";
        value.appendChild(el)
      });

//-----------------------------------------------------------------------------------------------------------------//
//initialize tiny slider
//-----------------------------------------------------------------------------------------------------------------//

      let slider = tns({
          container: value,
          items: 1,
          slideBy: "page",
          autoplay: false,
          autoplayButtonOutput: false,
          loop: true,
          mouseDrag: false,
          arrowKeys: true,
          gutter: 20,
          navPosition: "bottom",
          nav: true,
      });
    }); 
  };

//-----------------------------------------------------------------------------------------------------------------//
// https://github.com/ganlanyuan/tiny-slider documentation about tinyslider
//-----------------------------------------------------------------------------------------------------------------//
</script>
</html>