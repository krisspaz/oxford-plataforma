package com.oxford.shared.data.api

import io.ktor.client.*
import io.ktor.client.call.*
import io.ktor.client.plugins.contentnegotiation.*
import io.ktor.client.request.*
import io.ktor.http.*
import io.ktor.serialization.kotlinx.json.*
import kotlinx.serialization.json.Json

/**
 * Oxford API Client - Shared across iOS and Android
 */
class OxfordApiClient(
    private val baseUrl: String = "https://api.oxford.edu.gt"
) {
    private val httpClient = HttpClient {
        install(ContentNegotiation) {
            json(Json {
                ignoreUnknownKeys = true
                isLenient = true
                prettyPrint = false
            })
        }
    }

    private var authToken: String? = null

    fun setAuthToken(token: String) {
        authToken = token
    }

    fun clearAuthToken() {
        authToken = null
    }

    suspend inline fun <reified T> get(endpoint: String): Result<T> {
        return try {
            val response = httpClient.get("$baseUrl$endpoint") {
                authToken?.let {
                    header(HttpHeaders.Authorization, "Bearer $it")
                }
            }
            Result.success(response.body())
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend inline fun <reified T, reified R> post(endpoint: String, body: T): Result<R> {
        return try {
            val response = httpClient.post("$baseUrl$endpoint") {
                contentType(ContentType.Application.Json)
                authToken?.let {
                    header(HttpHeaders.Authorization, "Bearer $it")
                }
                setBody(body)
            }
            Result.success(response.body())
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    fun close() {
        httpClient.close()
    }
}
