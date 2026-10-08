import json
import re

transcript_path = r'C:\Users\Khushi Yadav\.gemini\antigravity-ide\brain\e33197b0-f4a4-4564-83f3-7b3e5d855577\.system_generated\logs\transcript_full.jsonl'
found_any = False

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
            if data.get('type') == 'PLANNER_RESPONSE':
                for call in data.get('tool_calls', []):
                    if call.get('name') == 'default_api:multi_replace_file_content':
                        args = call.get('arguments', {})
                        for chunk in args.get('ReplacementChunks', []):
                            content = chunk.get('TargetContent', '')
                            if 'form-wa-phone' in content:
                                with open('recovered_login.txt', 'w', encoding='utf-8') as out:
                                    out.write(content)
                                print("Found in TARGET CONTENT!")
                                found_any = True
                                break
                            
            if data.get('type') == 'USER_INPUT':
                content = data.get('content', '')
                if 'form-wa-phone' in content:
                    # User might have sent it in an edit
                    with open('recovered_login.txt', 'w', encoding='utf-8') as out:
                        out.write(content)
                    print("Found in USER_INPUT!")
                    found_any = True
        except Exception as e:
            pass

if not found_any:
    print('Still not found.')
