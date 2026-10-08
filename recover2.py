import json
import re

transcript_path = r'C:\Users\Khushi Yadav\.gemini\antigravity-ide\brain\e33197b0-f4a4-4564-83f3-7b3e5d855577\.system_generated\logs\transcript_full.jsonl'
target_tool_action = 'Updating password toggle in login page'
diff_output = None

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
            if data.get('type') == 'TOOL_RESPONSE' and data.get('tool_name') == 'default_api:multi_replace_file_content':
                out = data.get('content', '')
                if '@@ -209,7 +209,7 @@' in out or '@@ -201,247 +201,434 @@' in out:
                    if '@@ -201,247 +201,434 @@' in out:
                        diff_output = out
        except:
            pass

if diff_output:
    lines = diff_output.split('\n')
    extracted = []
    in_diff = False
    for line in lines:
        if line.startswith('@@'):
            in_diff = True
            continue
        if line.startswith('[diff_block_end]'):
            in_diff = False
            continue
        if in_diff:
            # We want to extract the DELETED lines and context lines!
            if line.startswith('-'):
                extracted.append(line[1:])
            elif line.startswith(' '):
                extracted.append(line[1:])
    
    # We also need to get the first 200 lines of the file, which didn't change!
    with open(r'c:\Users\Khushi Yadav\Desktop\acetech\schoolbag.in\resources\views\student\auth\login.blade.php', 'r', encoding='utf-8') as orig:
        orig_lines = orig.read().split('\n')
    
    final = orig_lines[:200] + extracted
    
    with open('recovered_login.blade.php', 'w', encoding='utf-8') as f:
        f.write('\n'.join(final))
    print(f'Recovered {len(final)} lines to recovered_login.blade.php')
else:
    print('Still not found.')
