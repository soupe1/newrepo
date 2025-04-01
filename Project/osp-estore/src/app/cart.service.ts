import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class CartService {
  private orderInfo: any = null;

  constructor() { }

  setOrderInfo(orderInfo: any) {
    this.orderInfo = orderInfo;
  }

  getOrderInfo() {
    return this.orderInfo;
  }

  clearOrderInfo() {
    this.orderInfo = null;
  }
}