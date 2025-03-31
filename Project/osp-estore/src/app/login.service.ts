import { Injectable, inject } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class LoginService {
  cookieService = inject(CookieService);

  private loggedIn = new BehaviorSubject(false);
  private loggedAdmin = new BehaviorSubject(false);
  private loggedName = new BehaviorSubject('');
  loggedInObs = this.loggedIn.asObservable();
  loggedAdminObs = this.loggedAdmin.asObservable();
  loggedNameObs = this.loggedName.asObservable();

  refreshLoginState() {
    if (this.cookieService.check('logged-type') && this.cookieService.check('logged-name')) {
      this.loggedIn.next(true);
      this.loggedAdmin.next(this.cookieService.get('logged-type') == 'admin');
      this.loggedName.next(this.cookieService.get('logged-name'));
    }
  }

  login() {
    this.loggedIn.next(true);
    this.loggedAdmin.next(this.cookieService.get('logged-type') == 'admin');
    this.loggedName.next(this.cookieService.get('logged-name'));
  }

  logout() {
    this.cookieService.delete('logged-name');
    this.cookieService.delete('logged-type');
    localStorage.removeItem('cart');
    this.loggedIn.next(false);
    this.loggedAdmin.next(false);
    this.loggedName.next('');
    window.location.reload();
  }

  getLoginState() {
    return this.loggedIn.value
  }

  getLoginType() {
    return this.loggedAdmin.value
  }

  getLoginName() {
    return this.loggedName.value
  }
}
