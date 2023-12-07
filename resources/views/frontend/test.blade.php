<script>
    const numArrays = JSON.parse({!! json_encode($sortArray) !!});
    // console.log(numArrays)
    // let element = null;
    // console.log(typeof numArrays)
    // let object;
    //  const keys = Object.keys(numArrays);
    let fox = null;
    // console.log(Object.values(numArrays))
    numArrays.forEach(function (list) {
        console.log(list);
    })
    //  Object.values(numArrays).forEach(function(str) {
    //      // console.log(str.indexOf);
    //
    //     let a = str, count = str.length;
    //     var missing = new Array();
    //
    //     for(let i=1; i<=count; i++) {
    //         if(a.indexOf(i) == -1){
    //             missing.push(i);
    //         }
    //     }
    //
    //
    //     // Object.values(element).forEach(function (end) {
    //     //
    //     // });
    //
    //     fox = missing;
    //     console.log(fox);
    // });
    // console.log(fox);


</script>
