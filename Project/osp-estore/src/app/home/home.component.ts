import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-home',
  imports: [FormsModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
  http = inject(HttpClient)

  draggable = {
    // note that data is handled with JSON.stringify/JSON.parse
    // only set simple data or POJO's as methods will be lost
    data: "myDragData",
    effectAllowed: "all",
    disable: false,
    handle: false
  };

  department_select = 'electronics'
  items: any[] = []

  item_dropped = false
  current_timeout: any = null

  ngOnInit() {
    this.updateItemList()
  }

  updateItemList() {
    this.http.post('http://localhost/CPS630/Project/backend/php/retrieve-items.php', {department_select: this.department_select}, {observe: 'response'}).subscribe(res => {
      this.items = JSON.parse(JSON.stringify(res.body))
    })
  }

  drag(event: DragEvent) {
    event.dataTransfer?.setData("item", (event.target as HTMLDivElement).getAttribute('data-value') || '')
  }

  allowDrop(event: DragEvent) {
    event.preventDefault();
  }

  drop(event: DragEvent) {
    event.preventDefault();
    var data = event.dataTransfer?.getData("item");
    var cart = localStorage.getItem('cart') || null;
    
    if (cart == null) {
      localStorage.setItem('cart', JSON.stringify([data]));
    } else {
      var cart_json = JSON.parse(cart)
      cart_json.push(data);
      localStorage.setItem('cart', JSON.stringify(cart_json));
    }

    if (this.current_timeout != null) {
      clearTimeout(this.current_timeout)
    }

    this.item_dropped = true
    this.current_timeout = setTimeout(() => {
      this.item_dropped = false
    }, 2500);
  }
}
