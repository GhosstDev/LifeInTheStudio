function getTrips() {
    $.ajax({
        url: "/assets/php/trips.php?action=getTrips",
        success: function(response){
            if (response.startsWith("err_")) {

            } else {
                $(".content").html(response);
            }
        } 
    });
}

getTrips();