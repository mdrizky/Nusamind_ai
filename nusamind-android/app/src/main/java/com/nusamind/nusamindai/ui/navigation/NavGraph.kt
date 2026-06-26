package com.nusamind.nusamindai.ui.navigation

import androidx.compose.runtime.Composable
import androidx.navigation.NavType
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import androidx.navigation.navArgument
import com.nusamind.nusamindai.ui.auth.LoginScreen
import com.nusamind.nusamindai.ui.auth.RegisterScreen
import com.nusamind.nusamindai.ui.splash.SplashScreen
import com.nusamind.nusamindai.ui.home.HomeScreen
import com.nusamind.nusamindai.ui.business.BusinessScreen
import com.nusamind.nusamindai.ui.product.ProductListScreen
import com.nusamind.nusamindai.ui.product.ProductFormScreen
import com.nusamind.nusamindai.ui.finance.FinanceScreen
import com.nusamind.nusamindai.ui.content.ContentScreen
import com.nusamind.nusamindai.ui.notification.NotificationScreen

object Routes {
    const val SPLASH = "splash"
    const val LOGIN = "login"
    const val REGISTER = "register"
    const val HOME = "home"
    const val BUSINESS = "business"
    const val PRODUCTS = "products"
    const val PRODUCT_FORM = "product_form/{productId}"
    const val FINANCE = "finance"
    const val CONTENT = "content"
    const val NOTIFICATIONS = "notifications"
}

@Composable
fun NusamindNavGraph() {
    val navController = rememberNavController()

    NavHost(navController = navController, startDestination = Routes.SPLASH) {
        composable(Routes.SPLASH) {
            SplashScreen(onNavigateToLogin = {
                navController.navigate(Routes.LOGIN) {
                    popUpTo(Routes.SPLASH) { inclusive = true }
                }
            }, onNavigateToHome = {
                navController.navigate(Routes.HOME) {
                    popUpTo(Routes.SPLASH) { inclusive = true }
                }
            })
        }

        composable(Routes.LOGIN) {
            LoginScreen(onNavigateToRegister = {
                navController.navigate(Routes.REGISTER)
            }, onLoginSuccess = {
                navController.navigate(Routes.HOME) {
                    popUpTo(Routes.LOGIN) { inclusive = true }
                }
            })
        }

        composable(Routes.REGISTER) {
            RegisterScreen(onNavigateToLogin = {
                navController.popBackStack()
            }, onRegisterSuccess = {
                navController.navigate(Routes.HOME) {
                    popUpTo(Routes.REGISTER) { inclusive = true }
                }
            })
        }

        composable(Routes.HOME) {
            HomeScreen(
                onNavigateToBusiness = { navController.navigate(Routes.BUSINESS) },
                onNavigateToProducts = { navController.navigate(Routes.PRODUCTS) },
                onNavigateToFinance = { navController.navigate(Routes.FINANCE) },
                onNavigateToContent = { navController.navigate(Routes.CONTENT) },
                onNavigateToNotifications = { navController.navigate(Routes.NOTIFICATIONS) },
                onLogout = {
                    navController.navigate(Routes.LOGIN) {
                        popUpTo(Routes.HOME) { inclusive = true }
                    }
                },
            )
        }

        composable(Routes.BUSINESS) {
            BusinessScreen(onBack = { navController.popBackStack() })
        }

        composable(Routes.PRODUCTS) {
            ProductListScreen(
                onBack = { navController.popBackStack() },
                onAddProduct = { navController.navigate("product_form/0") },
                onEditProduct = { id -> navController.navigate("product_form/$id") },
            )
        }

        composable(
            route = Routes.PRODUCT_FORM,
            arguments = listOf(navArgument("productId") { type = NavType.IntType }),
        ) {
            ProductFormScreen(onBack = { navController.popBackStack() })
        }

        composable(Routes.FINANCE) {
            FinanceScreen(onBack = { navController.popBackStack() })
        }

        composable(Routes.CONTENT) {
            ContentScreen(onBack = { navController.popBackStack() })
        }

        composable(Routes.NOTIFICATIONS) {
            NotificationScreen(onBack = { navController.popBackStack() })
        }
    }
}
