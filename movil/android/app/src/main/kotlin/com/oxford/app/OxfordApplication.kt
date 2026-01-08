package com.oxford.app

import android.app.Application
import com.oxford.app.di.AppContainer
import com.oxford.app.di.DefaultAppContainer

class OxfordApplication : Application() {
    
    lateinit var container: AppContainer
    
    override fun onCreate() {
        super.onCreate()
        instance = this
        container = DefaultAppContainer(this)
    }
    
    companion object {
        lateinit var instance: OxfordApplication
            private set
    }
}
