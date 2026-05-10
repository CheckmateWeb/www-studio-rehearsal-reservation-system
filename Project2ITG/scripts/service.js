function addFunc() {
    var firstName = document.getElementById("FName").value.trim();
    var lastName  = document.getElementById("LName").value.trim();

    if (firstName.length < 2 || firstName.length > 50 || lastName.length < 2 || lastName.length > 50) {
        Swal.fire({
            title: "Error",
            text: "First name and last name must be between 2 and 50 characters long",
            icon: "error",
            confirmButtonText: "OK",
        });
        return;
    }

    alert("Validation passed for add function");

    Swal.fire({
        title: "Confirm",
        text: "Are you sure you want to add this user?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, add it",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/Controller.php",
                type: "POST",
                data: {
                    fName: firstName,
                    lName: lastName
                },
                success: function (returnedData) {
                    Swal.fire({
                        title: "Good job!",
                        text: "Succesfully added a user named " + firstName + " " + lastName,
                        icon: "success",
                        confirmButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(true);
                        }
                    });
                },
                error: function (xhr) {
                    alert(xhr.status + " : " + xhr.responseText);
                }
            });
        }
    });
}

function updateFunc(userID) {
    var firstName = document.getElementById("FName").value.trim();
    var lastName  = document.getElementById("LName").value.trim();

    if (firstName.length < 2 || firstName.length > 50 || lastName.length < 2 || lastName.length > 50) {
        Swal.fire({
            title: "Error",
            text: "First name and last name must be between 2 and 50 characters long",
            icon: "error",
            confirmButtonText: "OK",
        });
        return;
    }

    alert("Validation passed for update function");

    Swal.fire({
        title: "Confirm",
        text: "Are you sure you want to update this user?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, update it",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/Controller.php",
                type: "POST",
                data: {
                    fName: firstName,
                    lName: lastName,
                    uID: userID
                },
                success: function (returnedData) {
                   Swal.fire({
                        title: returnedData,
                        text: "Succesfully updated a user to " + firstName + " " + lastName,
                        icon: "warning",
                        confirmButtonText: "OK",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(true);
                        }
                    });
                },
                error: function (xhr) {
                    alert(xhr.status + " : " + xhr.responseText);
                }
            });
        }
    });
}

function deleteFunc(userID) {
    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: {
            dID: userID
        },
        success: function (returnedData) {
            Swal.fire({
                title: "Good job!",
                text: "Succesfully deleted a user",
                icon: "error",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
        },
        error: function (xhr) {
            alert(xhr.status + " : " + xhr.responseText);
        }
    });
}

function redirectFunc(redirectID){
    if(redirectID == 1){
        window.location.href = "../views/login.php";
    }else if(redirectID == 2){
        window.location.href = "../views/dashboard.php"; 
    }else if(redirectID == 3){
        window.location.href = "../views/registrationpage.php";
    }
}

function loginFunc(){
    var LoginfirstName = document.getElementById("icon_LFName").value.trim();
    var LoginlastName  = document.getElementById("icon_LLName").value.trim();

    if (!LoginfirstName || !LoginlastName || LoginfirstName.length < 2 || LoginfirstName.length > 50 || LoginlastName.length < 2 || LoginlastName.length > 50) {
        Swal.fire({
            title: "Error",
            text: "Please enter valid first and last name (between 2 and 50 characters each)",
            icon: "error",
            confirmButtonText: "OK",
        });
        return;
    }

    alert("Validation passed for login function");

    Swal.fire({
        title: "Confirm",
        text: "Are you sure you want to login?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, login",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/Controller.php",
                type: "POST",
                dataType: "json",
                data: {
                    lFName: LoginfirstName,
                    lLName: LoginlastName
                },
                success: function (returnedData) {
                    if(returnedData.success){
                        Swal.fire({
                            title: "Success!",
                            text: "Login successful",
                            icon: "success",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                redirectFunc(2);
                            }
                        });
                    }else{
                        Swal.fire({
                            title: "Invalid Credentials",
                            text: returnedData.message || "User Not Found",
                            icon: "error",
                            confirmButtonText: "OK",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    }
                },
                error: function (xhr) {
                    alert("Error: " + xhr.status + " - " + xhr.responseText);
                }
            });
        }
    });
}