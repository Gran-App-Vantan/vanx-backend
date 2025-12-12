<img src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/banner.png" alt="カジノゲームとの連携機能を持つハイスペックSNS!" />

<br />

## アプリケーション概要
KADOKAWAドワンゴ情報工科学院で実施された、<b>「VANTAN文化祭2025」</b>の高等部2年の出展物として、制作を進めていきました。<br />
このアプリでは基本的なSNSの機能に加え、校舎のフロアマップ、カジノゲームのウォレット、ランキング、ルールブックなど様々な機能を兼ね備えています。

<br />

## サービスへの想い
<p>今回の文化祭では複数の校舎・フロアで実施されているため、リアルタイムでどのような事が起きているのか、実際にブースに参加した人の声などが伝わりにくくなっていました。</p>
<p>そこで我々はいつ・どこでも使えて、情報の伝達が素早い<b>SNS</b>を制作しようと考えました。</p>
<p>このサービスの狙いとしては、「◯◯のブースではこんな事をやってるんだ」「このブース面白そうだな」と興味を持ってもらい、文化祭をより盛り上げることが目的となっています。</p>

<br />

## 工夫した箇所・苦労した箇所
### フロントエンド
- UI・UXについては常に意識をしながら制作をしていて、デザインの段階からユーザー側の視点に立って設計を行いました。
- 型定義をする際には、単一情報源を意識しながら制作を進めていきました。
- API疎通の際に、リクエスト・レスポンス形式の不一致が多く発生した点はとても苦労しました。

### バックエンド
- オリジナルのプロバイダーを作成することによってカジノ側とのアクセスを強固にしました。
- テーブル設計が難しく、3社多体多の実装に困りました。

<br />

## 制作メンバー
<table>
  <tr>
    <th>プロダクトマネージャー</th>
    <td>
      <div>舟橋諒真・山本淳平</div>
    </td>
  </tr>
  <tr>
    <th>
      バックエンドエンジニア
    </th>
    <td>
      舟橋諒真
    </td>
  </tr>
  <tr>
    <th>
      フロントエンドエンジニア
    </th>
    <td>
      <div>山本淳平・上野獅旺・鈴木至恩・加藤雄大（助っ人枠: 加藤晴快）</div>
    </td>
  </tr>
  <tr>
    <th>
      デザイナー
    </th>
    <td>
      <div>山本淳平・上野獅旺・鈴木至恩</div>
    </td>
  </tr>
</table>

<br />

## 技術スタック
<table>
  <tr>
    <th>フロントエンド</th>
    <td>
      <div>
        <img src="https://img.shields.io/badge/-React-444444?logo=react&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-Next.js-444444?logo=next.js&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-TypeScript-444444?logo=typescript&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-TailwindCSS-444444?logo=tailwindcss&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-Axios-444444?logo=axios&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-Prettier-444444?logo=prettier&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-ESLint-444444?logo=eslint&style=for-the-badge" />
      </div>
    </td>
  </tr>
  <tr>
    <th>バックエンド</th>
    <td>
      <div>
        <img src="https://img.shields.io/badge/-Laravel-444444?logo=laravel&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-PHP-444444?logo=php&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-MySQL-444444?logo=mysql&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-Composer-444444?logo=composer&style=for-the-badge" />
      </div>
    </td>
  </tr>
  <tr>
    <th>デザインツール</th>
    <td>
      <div>
        <img src="https://img.shields.io/badge/-Figma-444444?logo=figma&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-AdobeIllustrator-444444?logo=adobeillustrator&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-AdobePhotoshop-444444?logo=adobephotoshop&style=for-the-badge" />
      </div>
    </td>
  </tr>
  <tr>
    <th>開発ツール</th>
    <td>
      <div>
        <img src="https://img.shields.io/badge/-Docker-444444?logo=docker&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-Git-444444?logo=git&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-GitHub-444444?logo=github&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-MakeFile-444444?logo=make&style=for-the-badge" />
      </div>
    </td>
  </tr>
  <tr>
    <th>デプロイツール</th>
    <td>
      <div>
        <img src="https://img.shields.io/badge/-EC2-444444?logo=ec2&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-RDS-444444?logo=rds&style=for-the-badge" />
        <img src="https://img.shields.io/badge/-VPC-444444?logo=vpc&style=for-the-badge" />
      </div>
    </td>
  </tr>
</table>

<br />

## 主な機能
| 機能名 | 詳細 |
|:------|:------|
|**投稿機能**|文化祭で楽しめたこと・共有したいことを文章や画像・動画にして投稿できます！|
|**リアクション機能**|投稿に対して好きなリアクションを付けることができます！|
|**フロアマップ機能**|このマップで、校舎の各フロアでどんなブースがあるかを知ることができます！|
|**ウォレット機**|カジノゲームで自分のポイントがどれくらい増えたのか、減ったのかを記録として見ることができます！|
|**ランキング機**|所持しているポイントで他のユーザーとランキングを競うことができます！|
|**カジノゲームとの連携機能**|カジノゲームとSNSのアカウントを連携して、ポイントの増減を管理することができます！|

<br />

## アプリの画面
<table>
  <tr>
    <th>サインアップページ</th>
    <th>ログインページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/signup-page.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/login-page.png"
        style="width: 40%;"
      />
    </td>
  </tr>
  <tr>
    <th>トップページ</th>
    <th>投稿ページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/home-page.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/post-page.png" 
        style="block; width: 40%;"
      />
    </td>
  </tr>
  <tr>
    <th>リアクション機能</th>
    <th>フロマップページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/reaction-sheet.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/floor-map-page.png"
        style="block; width: 40%;"
      />
    </td>
  </tr>
  <tr>
    <th>プロフィールページ</th>
    <th>プロフィール編集ページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/profile-page.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/profile-edit-page.png"
        style="block; width: 40%;"
      />
    </td>
  </tr>
  <tr>
    <th>ウォレットページ</th>
    <th>ランキングページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/wallet-page.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/ranking-page.png"
        style="block; width: 40%;"
      />
    </td>
  </tr>
  <tr>
    <th>ルールブックページ</th>
    <th>接続ページ</th>
  </tr>
  <tr>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/rule-book-page.png"
        style="block; width: 40%;"
      />
    </td>
    <td align="center">
      <img 
        src="https://github.com/Gran-App-Vantan/vanx-frontend/blob/dev/readmeImage/pages/connection-page.png"
        style="block; width: 40%;"
      />
    </td>
  </tr>
</table>
