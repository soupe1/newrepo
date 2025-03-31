import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormControl, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { LoginService } from '../login.service';

@Component({
  selector: 'app-signup',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './signup.component.html',
  styleUrl: './signup.component.css'
})
export class SignupComponent {
  http = inject(HttpClient)
  router = inject(Router)
  loginService = inject(LoginService)
  error = '';

  signupForm = new FormGroup({
    firstName: new FormControl('', [Validators.required, Validators.maxLength(120)]),
    lastName: new FormControl('', [Validators.required, Validators.maxLength(120)]),
    phone: new FormControl('', [Validators.required, Validators.maxLength(10), Validators.pattern("^[0-9]{10}$")]),
    email: new FormControl('', [Validators.required, Validators.maxLength(320)]),
    address: new FormControl('', [Validators.required]),
    city: new FormControl('', [Validators.required]),
    province: new FormControl('', [Validators.required]),
    country: new FormControl('', [Validators.required]),
    postalCode: new FormControl('', [Validators.required, Validators.pattern("^[A-z]\\d[A-z] ?\\d[A-z]\\d$")]),
    username: new FormControl('', [Validators.required, Validators.maxLength(255)]),
    password: new FormControl('', [Validators.required, Validators.minLength(8), Validators.maxLength(255)]),
    type: new FormControl('user')
  })

  onSubmit() {
    this.http.post('http://localhost/CPS630/Project/backend/php/sign-up.php', this.signupForm.value, {observe: 'response', responseType: 'json', withCredentials: true}).subscribe(res => {
      let res_json = JSON.parse(JSON.stringify(res.body));
      if (res_json.error_code == 0) {
        this.loginService.login()
        this.router.navigate(['/'])
        this.error = '';
      } else {
        this.error = res_json.error + " Please try again."
        window.scroll({
          top: 0,
          behavior: 'smooth'
        })
      }
    })
  }
}
