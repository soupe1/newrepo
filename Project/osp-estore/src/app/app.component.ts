import { Component, inject } from '@angular/core';
import { RouterOutlet, RouterLink, Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';
import { LoginService } from './login.service';
import { HttpClient } from '@angular/common/http';
import { catchError } from 'rxjs';

@Component({
    selector: 'app-root',
    imports: [RouterOutlet, RouterLink],
    templateUrl: './app.component.html',
    providers: [CookieService, Router],
    styleUrl: './app.component.css'
})
export class AppComponent {
    cookieService = inject(CookieService);
    loginService = inject(LoginService);
    http = inject(HttpClient)
    loggedIn = false;
    loggedAdmin = false;
    loggedName = '';

    ngOnInit() {
        this.http.get('http://localhost/CPS630/Project/backend/php/database-setup.php', {observe: 'response'}).subscribe()

        this.loginService.refreshLoginState()

        this.loginService.loggedInObs.subscribe(res => {
            this.loggedIn = res
        })

        this.loginService.loggedAdminObs.subscribe(res => {
            this.loggedAdmin = res
        })

        this.loginService.loggedNameObs.subscribe(res => {
            this.loggedName = res
        })
    }

    logout() {
        this.loginService.logout()
    }

    clearFocus() {
        if (document.activeElement instanceof HTMLElement) {
            document.activeElement.blur();
        }
    }
}
