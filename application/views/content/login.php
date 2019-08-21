<div class="row d-flex justify-content-center page-content-section">

  <div class="col-11 col-sm-9 col-md-6 col-lg-6 col-xl-4 right-font">

    <ul class="nav nav-pills nav-justified mb-3" role="tablist">
      <li class="nav-item">
        <a class="nav-link <?= $active_tab === 'login' ? 'active' : '' ?>" id="login-pill" data-toggle="pill" href="#login-tab-pane" role="tab" aria-controls="login" aria-selected="true">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $active_tab === 'signup' ? 'active' : '' ?>" id="signup-pill" data-toggle="pill" href="#signup-tab-pane" role="tab" aria-controls="signup" aria-selected="false">Sign Up</a>
      </li>
    </ul>

    <div class="tab-content bg-darkest text-white">
      <div id="login-tab-pane" class="tab-pane fade
        <?= $active_tab === 'login' ? 'active show' : '' ?>
      " role="tabpanel" aria-labelledby="login-pill">

        <?= form_error('login_form', '<p class="text-danger">', '</p>') ?>

        <?= form_open('user/login', 'id="login-form" novalidate') ?>

          <div class="form-group">
            <label for="login-name">Username/Email address</label>
            <input type="text" class="form-control text-dark" id="login-name" value="<?= set_value('login-name') ?>" name="login-name" autocomplete="off" required>
            <div class="invalid-feedback">
              Enter valid username/email
            </div>
          </div>
          <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" class="form-control text-dark" id="login-password" value="<?= set_value('login-password') ?>" name="login-password" autocomplete="current-password" required>
            <div class="invalid-feedback">
              Enter a correct password
            </div>
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="login-remember-me" name="login-remember-me" value="<?= set_value('login-remember-me') ?>">
            <label class="form-check-label" for="remember-me">Remember Me</label>
          </div>
          <br>
          <button type="submit" class="btn btn-info">Login</button>

        <?= form_close() ?>

      </div>

      <div id="signup-tab-pane" class="tab-pane fade
        <?= $active_tab === 'signup' ? 'active show' : '' ?>
      " role="tabpanel" aria-labelledby="signup-pill">

        <?= form_error('signup_form', '<p class="text-danger">', '</p>') ?>

        <?= form_open('user/signup', 'id="signup-form" novalidate') ?>

          <div class="form-group">
            <label>Full Name</label>
            <div class="row">
              <div class="col-6">
                <input type="text" id="signup-firstname" class="form-control text-dark <?= form_error('signup-firstname') ? 'is-invalid' : '' ?>" value="<?= set_value('signup-firstname') ?>" name="signup-firstname" placeholder="First" required/>
                <div class="invalid-feedback">
                  Enter a valid first-name.
                </div>
              </div>
              <div class="col-6">
                <input type="text" id="signup-lastname" class="form-control text-dark <?= form_error('signup-lastname') ? 'is-invalid' : '' ?>" value="<?= set_value('signup-lastname') ?>" name="signup-lastname" placeholder="Last" required />
                <div class="invalid-feedback">
                  Enter a valid last-name.
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="signup-username">Username (display name)</label>
            <input type="text" id="signup-username" class="form-control text-dark <?= form_error('signup-username') ? 'is-invalid' : '' ?>" value="<?= set_value('signup-username') ?>" name="signup-username" placeholder="Max. 20 characters" maxlength="20" autocomplete="username" required />
            <div class="valid-feedback">
              Username available!
            </div>
            <div class="invalid-feedback">
              Enter a username that doesn't already exist. It should be atmost 20 characters; consisting of alphanumeric (letters A-Z, numbers 0-9) or underscores (only between the alphanumeric characters).
            </div>
          </div>
          <div class="form-group">
            <label for="signup-email">Email</label>
            <input type="email" id="signup-email" class="form-control text-dark <?= form_error('signup-email') ? 'is-invalid' : '' ?>" value="<?= set_value('signup-email') ?>" autocomplete="email" name="signup-email" placeholder="" required />
            <div class="valid-feedback">
              Email is unique!
            </div>
            <div class="invalid-feedback">
              Enter a valid email that doesn't already exist.
            </div>
          </div>
          <div class="form-group">
            <label for="signup-password">Password</label>
            <div class="input-group">
              <input type="password" id="signup-password" minLength="8" class="form-control text-dark <?= form_error('signup-password') ? 'is-invalid' : '' ?>" value="<?= set_value('signup-password') ?>" autocomplete="new-password" name="signup-password" placeholder="Min. 8 characters" required />
              <div class="input-group-append">
                <div class="btn btn-secondary password-hidden password-visibility-toggle">
                  <span class="glyphicon glyphicon-eye-open"></span>
                </div>
              </div>
              <div class="invalid-feedback">
                Enter a Password with atleast 8 characters.
              </div>
            </div>
          </div>
          <div>
            By creating an account you agree to QuePenny's <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a>.
          </div>
          <br>
          <button type="submit" class="btn btn-success">Create Account</button>

        <?= form_close() ?>

      </div>
    </div>

  </div>

</div>
