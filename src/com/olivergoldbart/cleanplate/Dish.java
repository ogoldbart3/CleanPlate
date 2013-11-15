package com.olivergoldbart.cleanplate;

public class Dish {

    String name, description;
    Double price;
    int dishID, menuID, restaurantID;

    public Dish( int dishID, int menuID, int restaurantID, String name, String description, Double price ) {
        this.dishID = dishID;
        this.menuID = menuID;
        this.restaurantID = restaurantID;
        this.name = name;
        this.description = description;
        this.price = price;
    }

    String getName() {
        return name;
    }

    String getDescription() {
        return description;
    }

    Double getPrice() {
        return price;
    }

    int getDishID() {
        return dishID;
    }

    int getMenuID() {
        return menuID;
    }

    int getRestaurantID() {
        return restaurantID;
    }

    void setName( String name ) {
        this.name = name;
    }

    void setDescription( String description ) {
        this.description = description;
    }

    void setPrice( Double price ) {
        this.price = price;
    }

    void setDishID( int dishID ) {
        this.dishID = dishID;
    }

    void setMenuID( int menuID ) {
        this.menuID = menuID;
    }

    void setRestaurantID( int restaurantID ) {
        this.restaurantID = restaurantID;
    }

    public String toString() {
        String returnString = getName() + ", " + getDescription() + ", " + getPrice() + ", " + getDishID() + ", " + getMenuID() + ", " + getRestaurantID();
        return returnString;
    }


}
