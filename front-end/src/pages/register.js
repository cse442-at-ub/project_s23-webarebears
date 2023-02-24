import React from "react";
import './register.css'

const Register = () => {
    return(
        <div className="page">
            <div className="register-title">
                <div>SIGN UP WITH YOUR</div>
                <div>USERNAME</div>
            </div>
            
            <div className="form">
              <div className="form-body">
                  <div className="username">
                      <input  type="username" id="username" className="form__input" placeholder="Enter Your Username"/>
                  </div>
                  <div className="password">
                      <input className="form__input" type="password"  id="password" placeholder="Enter Your Password"/>
                  </div>
                  <div className="confirm-password">
                      <input className="form__input" type="password" id="confirmPassword" placeholder="Re-enter Password"/>
                  </div>
              </div>
              <div class="footer">
                  <button type="submit" class="btn">Register</button>
              </div>
          </div>      
        </div>
    );
}

export default Register;
