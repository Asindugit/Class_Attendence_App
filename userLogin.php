<?php include('allhead.php'); ?> 
</nav>

<style>

html,
body {
    height: 100%;
    margin: 0;
    padding: 0;
}

/* Full page background */
.bg {
    background-image: url('img/login.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 30px 15px;
    box-sizing: border-box;
}

/* Login box */
.login-box {
    width: 100%;
    max-width: 420px;

    background: rgba(255, 255, 255, 0.95);

    padding: 30px 35px;

    border-radius: 12px;

    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);

    box-sizing: border-box;
}

/* Header */
.login-title {
    text-align: center;
    margin: 0 0 30px 0;
    padding-bottom: 15px;

    font-size: 26px;
    font-weight: 600;

    border-bottom: 1px solid #ddd;
}

.login-title i {
    margin-right: 8px;
}

/* Form fields */
.login-box label {
    font-weight: 600;
    margin-bottom: 8px;
}

.login-box .form-control {
    height: 45px;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 15px;
}

/* Form group spacing */
.login-box .form-group {
    margin-bottom: 22px;
}

/* Buttons */
.login-buttons {
    text-align: center;
    margin-top: 28px;
}

.login-buttons .btn {
    min-width: 100px;
    height: 42px;

    margin: 0 5px;

    border-radius: 6px !important;

    font-weight: 600;
}

/* Mobile responsive */
@media (max-width: 576px) {

    .bg {
        padding: 20px 15px;
		margin-top: 5rem;
    }

    .login-box {
        max-width: 100%;
        padding: 25px 20px;
        border-radius: 10px;
    }

    .login-title {
        font-size: 23px;
        margin-bottom: 25px;
    }

    .login-box .form-control {
        height: 44px;
    }

    .login-buttons .btn {
        min-width: 95px;
        margin: 3px;
    }
}

</style>


<div class="bg">

    <div class="login-box">

        <!-- User Login -->
        <h3 class="login-title">
            <i class="fa-solid fa-user"></i>
            User Login
        </h3>

        <form name="facultylogin" action="loginlinkuser.php" method="POST">

            <!-- User ID -->
            <div class="control-group form-group">
                <div class="controls">

                    <label>User ID:</label>

                    <input
                        type="text"
                        class="form-control"
                        name="uid"
                        required
                        autocomplete="username"
                        data-validation-required-message="Please enter your Faculty Id."
                    >

                    <p class="help-block"></p>

                </div>
            </div>


            <!-- Password -->
            <div class="control-group form-group">
                <div class="controls">

                    <label>Password:</label>

                    <input
                        type="password"
                        class="form-control"
                        name="pass"
                        required
                        autocomplete="current-password"
                        data-validation-required-message="Please enter your password."
                    >

                    <p class="help-block"></p>

                </div>
            </div>


            <!-- Buttons -->
            <div class="login-buttons">

                <button type="submit" class="btn btn-primary">
                    Login
                </button>

                <button type="reset" class="btn btn-danger">
                    Reset
                </button>

            </div>

        </form>

    </div>

</div>

<?php include('allfoot.php'); ?>