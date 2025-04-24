import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/axios';

export default function Login() {
  const navigate = useNavigate();

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const login = async () => {
    setError('');
    try {
      // ✅ CSRFトークン取得（Cookieに XSRF-TOKEN がセットされる）
      await api.get('/sanctum/csrf-cookie');

      // ✅ ログインAPIにPOST（Cookie + CSRFで認証）
      await api.post('/login', { email, password });

      // ✅ 成功 → 一覧へ遷移
      navigate('/reflections');
    } catch (err) {
      console.error('Login failed', err);

      // ✅ エラーの種類によってメッセージ切り替え（Optional）
      if (err.response?.status === 422) {
        setError('バリデーションエラー：入力を確認してください');
      } else if (err.response?.status === 401) {
        setError('メールアドレスまたはパスワードが正しくありません');
      } else {
        setError('ログインに失敗しました');
      }
    }
  };

  return (
    <div>
      <h2>Login</h2>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <input
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
        autoComplete="username"
      />
      <input
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Password"
        type="password"
        autoComplete="current-password"
      />
      <button onClick={login}>Login</button>
    </div>
  );
}
