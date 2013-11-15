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

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        overridePendingTransition(R.anim.main_anim_in,R.anim.main_anim_out);
		
	} 
	

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.main, menu);
        return true;
    }
    
	
	public void randomDish( View view ) {
		
		
	    Stack<String> stack = new Stack<String>();
		stack.add("test");
		stack.add("test2");
		
	    ArrayList<CharSequence> arrayList = new ArrayList<CharSequence>();
	    
		Intent intent = new Intent();

		intent.putExtra("dishID", "1");
		intent.putExtra("sameMenu", false);
		intent.putCharSequenceArrayListExtra("arrayList", arrayList);
		
		intent.setClass(MainActivity.this, DishActivity.class);


        startActivity(intent);
        finish();

    }
	
}
