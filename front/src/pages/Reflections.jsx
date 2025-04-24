import { useEffect, useState } from 'react';
import api from '../api/axios';

export default function Reflections() {
  const [reflections, setReflections] = useState([]);

  useEffect(() => {

    api.get('/reflections')
      .then((res) => setReflections(res.data))
      .catch((err) => {
        console.error('一覧取得失敗', err);
      });
  }, []);

  return (
    <div>
      <h2>Reflections</h2>
      <ul>
        {reflections.map((r) => (
          <li key={r.id}>
            <strong>{r.quote}</strong><br />
            {r.response}
          </li>
        ))}
      </ul>
    </div>
  );
}
