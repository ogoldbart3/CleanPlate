package com.olivergoldbart.cleanplate;

public class Foodmenu {

    int FoodmenuID, restaurantID;
    FoodmenuCategory FoodmenuType;

    public Foodmenu( int FoodmenuID, int restaurantID, FoodmenuCategory FoodmenuType ) {
        this.FoodmenuID = FoodmenuID;
        this.restaurantID = restaurantID;
        this.FoodmenuType = FoodmenuType;
    }

    public FoodmenuCategory getFoodmenuType() {
        return FoodmenuType;
    }

    public int getFoodmenuID() {
        return FoodmenuID;
    }

    public int getRestaurantID() {
        return restaurantID;
    }


    public void setFoodmenuType(FoodmenuCategory FoodmenuType) {
        this.FoodmenuType = FoodmenuType;
    }

    public void setFoodmenuID(int FoodmenuID) {
        this.FoodmenuID = FoodmenuID;
    }

    public void setRestaurantID(int restaurantID) {
        this.restaurantID = restaurantID;
    }

}