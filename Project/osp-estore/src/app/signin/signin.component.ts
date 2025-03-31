import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormControl, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { LoginService } from '../login.service';

@Component({
  selector: 'app-signin',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './signin.component.html',
  styleUrl: './signin.component.css'
})
export class SigninComponent {
  rt = inject(ActivatedRoute);
  router = inject(Router)
  http = inject(HttpClient);
  loginService = inject(LoginService);
  source = '';
  error = '';

  loginForm = new FormGroup({
    username: new FormControl('', [Validators.required, Validators.maxLength(255)]),
    password: new FormControl('', [Validators.required, Validators.minLength(8), Validators.maxLength(255)])
  })

  ngOnInit() {
    if (this.loginService.getLoginState()) {
      this.router.navigate(['/'])
    } else {
      this.rt.queryParams
      .subscribe(params => {
        if (params['from-cart'] == 'true') {
           this.source = 'cart'
        } else {
          this.source = ''
        }
      })
    }
  }
  
  onSubmit() {
    this.http.post('http://localhost/CPS630/Project/backend/php/sign-in.php', this.loginForm.value, {observe: 'response', responseType: 'json', withCredentials: true}).subscribe(res => {
      let res_json = JSON.parse(JSON.stringify(res.body));
      if (res_json.error_code == 0) {
        if (res_json.user_exists == true) {
          this.loginService.login()
          if (this.source != '') {
            this.router.navigate(['/' + this.source])
          } else {
            this.router.navigate(['/'])
          }
          this.error = '';
        } else {
          this.error = res_json.error + " Please try again."
        }
      } else {
        this.error = res_json.error + " Please try again."
      }
    })
  }
}
