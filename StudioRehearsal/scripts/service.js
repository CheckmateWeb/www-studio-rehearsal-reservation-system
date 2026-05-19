function addFunc() {
    var firstName = document.getElementById("FName").value.trim();
    var lastName  = document.getElementById("LName").value.trim();

    if (!firstName || !lastName) {
        Swal.fire("Invalid Input", "First name and last name are required.", "error");
        return;
    }

    if (firstName.length < 2 || lastName.length < 2) {
        Swal.fire("Invalid Input", "First name and last name must be at least 2 characters.", "error");
        return;
    }

    if (firstName.length > 20 || lastName.length > 20) {
        Swal.fire("Invalid Input", "First name and last name must not exceed 20 characters.", "error");
        return;
    }

    if (!/^[a-zA-Z\s]+$/.test(firstName) || !/^[a-zA-Z\s]+$/.test(lastName)) {
        Swal.fire("Invalid Input", "First name and last name should only contain letters and spaces.", "error");
        return;
    }

    $.ajax({
        url: "../controllers/controller.php",
        type: "POST",
        data: {
            fName: firstName,
            lName: lastName
        },
        success: function (returnedData) {
            if (isSuccessfulTextResponse(returnedData, ["user has been added"])) {
                Swal.fire({
                    title: "Good job!",
                    text: "Successfully added a user named " + firstName + " " + lastName,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(true);
                    }
                });
            } else {
                Swal.fire("Error", returnedData, "error");
            }
        },
        error: function (xhr) {
            Swal.fire("Error", xhr.status + " : " + xhr.responseText, "error");
        }
    });
}

function updateFunc(userID) {
    var firstName = document.getElementById("FName").value.trim();
    var lastName  = document.getElementById("LName").value.trim();

    if (!firstName || !lastName) {
        Swal.fire("Invalid Input", "First name and last name are required.", "error");
        return;
    }

    if (firstName.length < 2 || lastName.length < 2) {
        Swal.fire("Invalid Input", "First name and last name must be at least 2 characters.", "error");
        return;
    }

    if (firstName.length > 20 || lastName.length > 20) {
        Swal.fire("Invalid Input", "First name and last name must not exceed 20 characters.", "error");
        return;
    }

    if (!/^[a-zA-Z\s]+$/.test(firstName) || !/^[a-zA-Z\s]+$/.test(lastName)) {
        Swal.fire("Invalid Input", "First name and last name should only contain letters and spaces.", "error");
        return;
    }

    $.ajax({
        url: "../controllers/controller.php",
        type: "POST",
        data: {
            fName: firstName,
            lName: lastName,
            uID: userID
        },
        success: function (returnedData) {
            if (isSuccessfulTextResponse(returnedData, ["user updated successfully"])) {
                Swal.fire({
                    title: "Updated!",
                    text: "Successfully updated a user to " + firstName + " " + lastName,
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(true);
                    }
                });
            } else {
                Swal.fire("Error", returnedData, "error");
            }
        },
        error: function (xhr) {
            Swal.fire("Error", xhr.status + " : " + xhr.responseText, "error");
        }
    });
}

function deleteFunc(userID) {
    Swal.fire({
        title: "Delete user?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    dID: userID
                },
                success: function (returnedData) {
                    if (isSuccessfulTextResponse(returnedData, ["user deleted successfully"])) {
                        Swal.fire({
                            title: "Deleted!",
                            text: returnedData,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire("Error", returnedData, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire("Error", xhr.status + " : " + xhr.responseText, "error");
                }
            });
        }
    });
}

function redirectFunc(redirectID) {
    if (redirectID == 1) {
        window.location.href = "../views/login.php";
    } else if (redirectID == 2) {
        window.location.href = "../views/dashboard.php";
    } else if (redirectID == 3) {
        window.location.href = "../views/registration.php";
    } else if (redirectID == 4) {
        window.location.href = "../views/admin.php";
    }
}

function validateNamePair(firstName, lastName) {
    if (!firstName || !lastName) {
        return "First name and last name are required.";
    }

    if (firstName.length < 2 || lastName.length < 2) {
        return "First name and last name must be at least 2 characters.";
    }

    if (firstName.length > 20 || lastName.length > 20) {
        return "First name and last name must not exceed 20 characters.";
    }

    if (!/^[a-zA-Z\s]+$/.test(firstName) || !/^[a-zA-Z\s]+$/.test(lastName)) {
        return "First name and last name should only contain letters and spaces.";
    }

    return "";
}

function validateEmailAddress(email) {
    if (email.length < 5 || email.length > 50) {
        return "Email must be 5 to 50 characters.";
    }

    if (!/^\S+@\S+\.\S+$/.test(email)) {
        return "Invalid email format.";
    }

    return "";
}

function getPasswordChecklist(password) {
    return {
        hasMinimumLength: password.length >= 8,
        hasLetterAndNumber: /[A-Za-z]/.test(password) && /\d/.test(password),
        hasSpecialCharacter: /[^A-Za-z0-9]/.test(password)
    };
}

function validateRegistrationPassword(password) {
    var checklist = getPasswordChecklist(password);

    if (password.length > 64) {
        return "Password must not exceed 64 characters.";
    }

    if (!checklist.hasMinimumLength || !checklist.hasLetterAndNumber) {
        return "Password must be at least 8 characters and include both letters and numbers.";
    }

    if (!checklist.hasSpecialCharacter) {
        return "Password must include at least one special character.";
    }

    return "";
}

function isSuccessfulTextResponse(responseText, allowedValues) {
    var normalized = (responseText || "").toString().trim().toLowerCase();
    return allowedValues.indexOf(normalized) !== -1;
}

function setButtonLoading(button, loading, loadingText) {
    var $button = $(button);
    if (!$button.length) {
        return;
    }

    if (loading) {
        $button.data('original-text', $button.text());
        $button.prop('disabled', true).addClass('loading').text(loadingText);
    } else {
        var originalText = $button.data('original-text') || loadingText;
        $button.prop('disabled', false).removeClass('loading').text(originalText);
    }
}

function loginFunc() {
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;

    if (!email || !password) {
        Swal.fire({
            title: "Error",
            text: "Please enter both email and password",
            icon: "error",
            confirmButtonText: "OK"
        });
        return;
    }

    var emailError = validateEmailAddress(email);
    if (emailError) {
        Swal.fire("Invalid Input", emailError, "error");
        return;
    }

    var $submitButton = $('#loginForm button[type="submit"]');
    setButtonLoading($submitButton, true, "ENTERING...");

    $.ajax({
        url: "../controllers/controller.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "login",
            email: email,
            password: password
        },
        success: function (returnedData) {
            if (returnedData.success) {
                Swal.fire({
                    title: "Success!",
                    text: "Login successful",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (returnedData.role === "admin") {
                            redirectFunc(4);
                        } else {
                            redirectFunc(2);
                        }
                    }
                });
            } else {
                Swal.fire({
                    title: "Invalid Credentials",
                    text: returnedData.message || "User Not Found",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        },
        error: function (xhr) {
            Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
        },
        complete: function () {
            setButtonLoading($submitButton, false, "ENTER STUDIO");
        }
    });
}

function registerFunc(form) {
    var firstName = document.getElementById("fName").value.trim();
    var lastName  = document.getElementById("lName").value.trim();
    var email     = document.getElementById("email").value.trim();
    var password  = document.getElementById("password").value;

    var formData = $(form).serialize();

    // ===== YOUR STYLE VALIDATION =====
    var nameError = validateNamePair(firstName, lastName);
    if (nameError) {
        Swal.fire("Invalid Input", nameError, "error");
        return;
    }

    var emailError = validateEmailAddress(email);
    if (emailError) {
        Swal.fire("Invalid Input", emailError, "error");
        return;
    }

    var passwordError = validateRegistrationPassword(password);
    if (passwordError) {
        Swal.fire("Invalid Input", passwordError, "error");
        return;
    }

    // ===== AJAX =====
    var $submitButton = $(form).find('button[type="submit"]');
    setButtonLoading($submitButton, true, "CREATING...");

    $.ajax({
        type: "POST",
        url: "../controllers/controller.php",
        data: formData + "&action=register",
        success: function(response) {
            if (isSuccessfulTextResponse(response, ["success"])) {
                Swal.fire("Success!", "Account created!", "success")
                .then(() => window.location.href = "login.php");
            } else {
                Swal.fire("Error", response, "error");
            }
        },
        error: function(xhr) {
            Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
        },
        complete: function() {
            setButtonLoading($submitButton, false, "CREATE ACCOUNT");
        }
    });
}

function reserveRoom(studio_id, studio_name, price) {
    Swal.fire({
        title: "Reserve Room?",
        text: "Do you want to reserve " + studio_name + "?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Reserve",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    action: "reserve",
                    studio_id: studio_id,
                    price: price
                },
                success: function(response) {
                    if (isSuccessfulTextResponse(response, ["success"])) {
                        Swal.fire({
                            title: "Reserved!",
                            text: studio_name + " has been reserved successfully.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(true);
                            }
                        });
                    } else {
                        Swal.fire("Error", response, "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
                }
            });
        }
    });
}

function deleteRoomFunc(id) {
    Swal.fire({
        title: "Delete room?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    dID: id
                },
                success: function(response) {
                    Swal.fire("Deleted!", response, "success")
                    .then(() => location.reload(true));
                },
                error: function(xhr) {
                    Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
                }
            });
        }
    });
}

function logout() {
    Swal.fire({
        title: "Logout?",
        text: "You will be signed out.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    action: "logout"
                },
                success: function(response) {
                    if (response.trim() === "success") {
                        redirectFunc(1);
                    } else {
                        Swal.fire("Error", "Logout failed", "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", xhr.status + " - " + xhr.responseText, "error");
                }
            });
        }
    });
}

function openUpdateModal(userID, firstName, lastName, email) {
    Swal.fire({
        title: "Update User",
        background: "#1a1a1a",
        color: "#ffffff",
        html: `
            <input id="swal-fname" class="swal2-input" placeholder="First Name" value="${firstName}">
            <input id="swal-lname" class="swal2-input" placeholder="Last Name" value="${lastName}">
            <input id="swal-email" class="swal2-input" placeholder="Email" value="${email}">
        `,
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        preConfirm: () => {
            const fName = document.getElementById("swal-fname").value.trim();
            const lName = document.getElementById("swal-lname").value.trim();
            const emailVal = document.getElementById("swal-email").value.trim();

            var nameError = validateNamePair(fName, lName);
            if (nameError) {
                Swal.showValidationMessage(nameError);
                return false;
            }

            var emailError = validateEmailAddress(emailVal);
            if (emailError) {
                Swal.showValidationMessage(emailError);
                return false;
            }

            return {
                fName: fName,
                lName: lName,
                email: emailVal
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/controller.php",
                type: "POST",
                data: {
                    uID: userID,
                    fName: result.value.fName,
                    lName: result.value.lName,
                    email: result.value.email
                },
                success: function(response) {
                    if (isSuccessfulTextResponse(response, ["user updated successfully"])) {
                        Swal.fire("Updated!", response, "success")
                        .then(() => location.reload(true));
                    } else {
                        Swal.fire("Error", response, "error");
                    }
                },
                error: function(xhr) {
                    Swal.fire("Error", xhr.responseText, "error");
                }
            });
        }
    });
}
