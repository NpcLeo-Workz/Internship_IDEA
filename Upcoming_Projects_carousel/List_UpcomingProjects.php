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
        
        if(strcmp($check[0],'Upcoming')==0 && stripos($filenameArray[5], '-')== false){
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
<div class="tartist-tiny-slider-wrap fullslider">
  <div class="tartist-tiny-slider" data-chunksize="6">
  </div>
</div>
<script >
//-----------------------------------------------------------------------------------------------------------------//
// makes an img tag in html for each image with the given attributes and ads it to a link
// the link is added to a subdivision of the carousel
//-----------------------------------------------------------------------------------------------------------------//

  Images.forEach(element => {

  //split up the name to fill all the fields for the card
  var elements = element.split("/");
  var elementinfo =(elements[elements.length -1]);
  var elementinfosplit = elementinfo.split("_");
  var elementstartdate = 'Startdate: '+ elementinfosplit[1].substring(0,2) + '/' + elementinfosplit[1].substring(2,4) + '/' + elementinfosplit[1].substring(4);
  var elementenddate= 'Enddate: '+ elementinfosplit[2].substring(0,2) + '/' + elementinfosplit[2].substring(2,4) + '/' + elementinfosplit[2].substring(4);
  var elementlocation= 'Location: '+ elementinfosplit[3];
  var elementname = "";
  elementinfosplit[elementinfosplit.length -1]=elementinfosplit[elementinfosplit.length -1].replace('.png', '');
  for(i =4;i<elementinfosplit.length;i++){
      elementname+= elementinfosplit[i]+" ";
  }
  
  var elem = document.createElement('img');
  var elementpath = 'https://www.idea-europa.eu/1646923756060' + element.substring(1);
  //console.log(elementpath);
  elem.src = elementpath;

  //putting the image into a link
  var elementlink = document.createElement('a');
  elementlink.href= elementpath;
  elementlink.target= '_blank';
  elementlink.rel = 'noopener noreferrer';
  elementlink.appendChild(elem);

  //making the info for the card
  var ProjName = document.createElement('h2');
  ProjName.textContent = elementname;
  var Projlocation = document.createElement('p');
  Projlocation.textContent = elementlocation;
  var Projstartdate = document.createElement('p');
  Projstartdate.textContent = elementstartdate;
  var Projenddate = document.createElement('p');
  Projenddate.textContent = elementenddate;

  //making a card and adding the link and info to it
  var carddiv = document.createElement('div');
  carddiv.className = 'card';
  var containerdiv = document.createElement('div');
  containerdiv.className = 'container';
  containerdiv.appendChild(ProjName);
  containerdiv.appendChild(Projlocation);
  containerdiv.appendChild(Projstartdate);
  containerdiv.appendChild(Projenddate);
  elementlink.appendChild(containerdiv);
  carddiv.appendChild(elementlink);
  
  

  var IDiv = document.createElement('div');
  IDiv.className = 'tartist-tiny-slider__item';
  IDiv.appendChild(carddiv);
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
}); 
</script>
</html>