package com.olivergoldbart.cleanplate;

import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.Stack;

import org.apache.http.client.HttpClient;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;

import android.os.AsyncTask;
import android.os.Bundle;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.widget.TextView;

import com.loopj.android.http.*;
import com.olivergoldbart.cleanplate.R;


public class MainActivity extends Activity {


	String currentDishID;
	String currentFoodmenuID;
	String currentRestaurantID;
	

	String url = "http://m3.cip.gatech.edu/d/ogoldbart3/w/cleanplate/c/api/";
	String urlAddon;
	
    ArrayList<CharSequence> arrayList;
	
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        overridePendingTransition(R.anim.main_anim_in,R.anim.main_anim_out);
		
        arrayList = new ArrayList<CharSequence>();
	} 
	

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
	
	public void randomDish( View view ) {
	    
	    urlAddon = "restaurant/0/randomOtherDish/";
		
		AsyncHttpClient client = new AsyncHttpClient();
		
		client.get( url + urlAddon, new AsyncHttpResponseHandler() {
			
			@Override
			public void onSuccess(String response) {
				try {
					JSONObject jsonDish = new JSONObject(response);
					
					try {
						currentRestaurantID = jsonDish.getString("restaurant_id");
						currentFoodmenuID = jsonDish.getString("foodmenu_id");
 						currentDishID = jsonDish.getString("dish_id");
 						
 						Intent intent = new Intent();
 						
 						arrayList.add(0, currentDishID);
 						intent.putCharSequenceArrayListExtra("arrayList", arrayList);
 						
 						intent.putExtra( "currentRestaurantID", currentRestaurantID );
 						intent.putExtra( "currentFoodmenuID", currentFoodmenuID );
 						intent.putExtra( "currentDishID", currentDishID );
 						
 						intent.setClass(MainActivity.this, DishActivity.class);

 						startActivity(intent);
 						finish();
					} catch (JSONException e) {
						Log.v("testcat", "testcat failed pulling ID");
					}
					
				} catch (JSONException e) {
					Log.v("testcat", "testcat failed");
				}	
			}
		});
    }
}
