import { useEffect, useState } from 'react';
import api from '../api/axios';

export default function Reflections() {
  const [reflections, setReflections] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [editData, setEditData] = useState({ quote: '', response: '' });
  const [newReflection, setNewReflection] = useState({ quote: '', response: '' });

  useEffect(() => {
    fetchReflections();
  }, []);

  const fetchReflections = () => {
    api.get('/reflections')
      .then((res) => setReflections(res.data))
      .catch((err) => console.error('一覧取得失敗', err));
  };

  const handleCreate = async () => {
    try {
      const res = await api.post('/reflections', newReflection);
      setReflections((prev) => [res.data, ...prev]);
      setNewReflection({ quote: '', response: '' });
    } catch (err) {
      console.error('作成失敗', err);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('本当に削除しますか？')) return;
    try {
      await api.delete(`/reflections/${id}`);
      setReflections((prev) => prev.filter((r) => r.id !== id));
    } catch (err) {
      console.error('削除失敗', err);
    }
  };

  const handleEditClick = (reflection) => {
    setEditingId(reflection.id);
    setEditData({ quote: reflection.quote, response: reflection.response });
  };

  const handleEditChange = (key, value) => {
    setEditData((prev) => ({ ...prev, [key]: value }));
  };

  const handleSave = async (id) => {
    try {
      const res = await api.put(`/reflections/${id}`, editData);
      setReflections((prev) => prev.map((r) => (r.id === id ? res.data : r)));
      setEditingId(null);
    } catch (err) {
      console.error('更新失敗', err);
    }
  };

  const handleCancel = () => {
    setEditingId(null);
  };

  return (
    <div className="max-w-2xl mx-auto p-6">
      <h2 className="text-2xl font-bold mb-4">Reflections</h2>

      {/* 新規作成フォーム */}
      <div className="mb-6 bg-white shadow-md rounded-md p-4">
        <input
          className="w-full border p-2 mb-2 rounded"
          value={newReflection.quote}
          onChange={(e) => setNewReflection((prev) => ({ ...prev, quote: e.target.value }))}
          placeholder="新しい quote"
        />
        <textarea
          className="w-full border p-2 mb-2 rounded"
          value={newReflection.response}
          onChange={(e) => setNewReflection((prev) => ({ ...prev, response: e.target.value }))}
          placeholder="新しい response"
        />
        <button onClick={handleCreate} className="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
          追加
        </button>
      </div>

      {/* 一覧表示 */}
      <div className="space-y-4">
        {reflections.map((r) => (
          <div key={r.id} className="bg-white shadow-md rounded-md p-4">
            {editingId === r.id ? (
              <>
                <input
                  className="w-full border p-2 mb-2 rounded"
                  value={editData.quote}
                  onChange={(e) => handleEditChange('quote', e.target.value)}
                />
                <textarea
                  className="w-full border p-2 mb-2 rounded"
                  value={editData.response}
                  onChange={(e) => handleEditChange('response', e.target.value)}
                />
                <div className="flex gap-2">
                  <button onClick={() => handleSave(r.id)} className="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    保存
                  </button>
                  <button onClick={handleCancel} className="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                    キャンセル
                  </button>
                </div>
              </>
            ) : (
              <>
                <h3 className="font-semibold text-lg">{r.quote}</h3>
                <p className="text-gray-700 mb-2">{r.response}</p>
                <div className="flex gap-2">
                  <button onClick={() => handleEditClick(r)} className="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">
                    編集
                  </button>
                  <button onClick={() => handleDelete(r.id)} className="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                    削除
                  </button>
                </div>
              </>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
