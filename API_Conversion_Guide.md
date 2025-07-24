# Laravel API 変換手順書

このドキュメントは、既存の Laravel アプリケーションを Next.js フロントエンドと分離した API アーキテクチャに変換するためのバックエンド（Laravel API）部分の手順を示しています。

## 0. APIとウェブルートの重複について

### 0.1 ルートの重複の問題
- Laravelでは、`web.php`と`api.php`で同じパスのルートを定義することは可能ですが、推奨されません。
- 重複したルートが存在すると、以下の問題が発生する可能性があります：
  - メンテナンス性の低下
  - ルートの衝突による予期せぬ動作
  - セキュリティ上の懸念

### 0.2 ルート名（name）の重複について
- Laravelでは、`web.php`と`api.php`で同じ名前のルートを定義することは可能ですが、推奨されません。
- 重複したルート名が存在すると、以下の問題が発生する可能性があります：
  - ルート名の衝突による予期せぬ動作
  - メンテナンス性の低下
  - ルート名の解決時の不確実性

### 0.3 ベストプラクティス
1. **ルート名の命名規則**
   - API用のルート名には`api.`プレフィックスを付加
   - 例：`api.register`, `api.login`など

2. **既存のルート名の整理**
   - API化する際は、既存のルート名を更新
   - 例：
     ```php
     // web.phpの既存ルート
     Route::get('/top', [TopController::class, 'index'])->name('test.top');
     
     // api.phpに追加
     Route::get('/api/top', [TopController::class, 'index'])->name('api.top');
     ```

3. **認証ミドルウェアの使用**
   - **認証が必要なエンドポイント**
     - ユーザー情報の取得
     - プロフィールの更新
     - 投稿の作成・編集・削除
     - リアクションの操作
     ```php
     // 認証が必要なエンドポイント
     Route::middleware('auth:sanctum')->group(function () {
         Route::get('/api/user', [AuthController::class,'profile']);
         Route::put('/api/user', [AuthController::class,'updateProfile']);
         Route::post('/api/posts', [PostController::class,'store']);
         Route::put('/api/posts/{id}', [PostController::class,'update']);
         Route::delete('/api/posts/{id}', [PostController::class,'destroy']);
         Route::post('/api/reactions', [ReactionController::class,'store']);
         Route::delete('/api/reactions/{id}', [ReactionController::class,'destroy']);
     });
     ```
   
   - **認証が不要なエンドポイント**
     - ユーザー登録
     - ログイン
     - 公開データの取得
     ```php
     // 認証が不要なエンドポイント
     Route::post('/api/register', [AuthController::class,'register']);
     Route::post('/api/login', [AuthController::class,'login']);
     Route::get('/api/public-data', [PublicController::class,'index']);
     ```

### 0.2 ベストプラクティス
1. **API専用のエンドポイント**
   - `api.php`では、`/api/*`のプレフィックスを使用してAPI専用のエンドポイントを定義
   - 例：`/api/register`, `/api/login`など

2. **ウェブアプリケーション用のルート**
   - `web.php`では、通常のウェブアプリケーション用のルートを定義
   - 例：`/test/accounts/register`, `/test/login`など

3. **ルートの整理**
   - 既存の`web.php`のルートを分析し、API化が必要なエンドポイントを特定
   - API化が必要なエンドポイントは`api.php`に移動し、`web.php`の該当ルートは削除
   - 例：
     ```php
     // web.php から削除
     Route::post('/register', [TopController::class,'register_store']);
     
     // api.php に追加
     Route::post('/api/register', [AuthController::class,'register']);
     ```

4. **セキュリティ設定**
   - API用のエンドポイントには`auth:sanctum`ミドルウェアを適用
   - ウェブアプリケーション用のエンドポイントには`auth`ミドルウェアを適用
   - 必要に応じて、異なる認証戦略を適用可能

### 0.3 例：ルートの整理
```php
// web.php 例（ウェブアプリケーション用）
Route::prefix('test')->group(function () {
    Route::get('/top', [TopController::class, 'index'])->name('test.top');
    Route::get('/', function () {
        return view('test.top');
    });
});

// api.php 例（API用）
Route::post('/api/register', [AuthController::class,'register']);
Route::post('/api/login', [AuthController::class,'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/user', [AuthController::class,'profile']);
    Route::put('/api/user', [AuthController::class,'updateProfile']);
    Route::get('/api/top', [TopController::class,'index']);
});

## 1. API 構造の設計

### 1.1 認証関連のエンドポイント
```php
// 認証API
POST /api/register - ユーザー登録
POST /api/login - ログイン
GET /api/user - プロフィール取得
PUT /api/user - プロフィール更新
```

### 1.2 トップページ関連のエンドポイント
```php
// トップページAPI
GET /api/top - トップページデータ取得
```

## 2. Laravel 側の変換手順

### 2.1 API コントローラーの作成
1. `app/Http/Controllers/Api` ディレクトリを作成
2. `AuthController.php` と `TopController.php` を作成

#### AuthController
- register メソッド: ユーザー登録
- login メソッド: ログイン処理
- profile メソッド: プロフィール取得
- updateProfile メソッド: プロフィール更新

#### TopController
- index メソッド: トップページデータ取得

### 2.2 ルーティングの設定
1. `routes/api.php` を編集
2. 認証不要なエンドポイントを定義
3. 認証が必要なエンドポイントを `auth:sanctum` ミドルウェアで保護

### 2.3 レスポンス形式の変更
1. ブレードテンプレートの代わりに JSON レスポンスを返す
2. エラーハンドリングを統一的な形式で実装
3. バリデーションエラーを適切にハンドリング

### 2.4 CORS 設定
1. `config/cors.php` の設定を確認
2. 必要に応じて許可するオリジンを設定

### 2.5 認証トークンの管理
1. Laravel Sanctum の設定を確認
2. API トークンの発行と管理を実装

## 3. テスト手順

1. Laravel API の単体テスト
2. 統合テスト
3. エンドツーエンドテスト

## 4. 注意点

1. CORS 設定の確認
2. 認証トークンの管理
3. エラーハンドリングの一貫性
4. バリデーションルールの統一
5. パフォーマンスの最適化

## 5. API レスポンス形式の例

### 5.1 成功時
```json
{
    "data": {
        // データ
    },
    "meta": {
        "status": "success",
        "timestamp": "2025-07-15T16:54:22+09:00"
    }
}
```

### 5.2 エラー時
```json
{
    "error": {
        "message": "エラーメッセージ",
        "code": "ERROR_CODE",
        "details": {
            // エラーの詳細
        }
    },
    "meta": {
        "status": "error",
        "timestamp": "2025-07-15T16:54:22+09:00"
    }
}
```

## 6. バリデーションエラーの例
```json
{
    "error": {
        "message": "Validation failed",
        "code": "VALIDATION_ERROR",
        "details": {
            "name": ["The name field is required."],
            "email": ["The email must be a valid email address."]
        }
    },
    "meta": {
        "status": "error",
        "timestamp": "2025-07-15T16:54:22+09:00"
    }
}
```

このドキュメントは、API 変換の基本的な手順を示しています。実際の実装時には、プロジェクトの要件に応じて適切に調整してください。
