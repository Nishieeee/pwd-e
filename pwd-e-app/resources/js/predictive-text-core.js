// Context-aware vocabulary. Each context gets a focused word set to reduce noise.
const COMMON = [
    'a','an','the','and','or','but','if','then','else','when','while','because','although','though','unless','until','since','as','so','yet','nor','both','either','neither','not','only','also','just','even','still','already','always','never','ever','often','sometimes','usually','really','very','too','quite','rather','more','most','less','least','much','many','few','some','any','all','no','none','one','two','three','each','every','other','another','same','such','what','which','who','whom','whose','where','when','why','how','this','that','these','those','i','you','he','she','it','we','they','me','him','her','us','them','my','your','his','its','our','their','mine','yours','ours','theirs','myself','yourself','himself','herself','itself','ourselves','themselves','be','am','is','are','was','were','been','being','have','has','had','having','do','does','did','doing','will','would','shall','should','may','might','must','can','could','need','dare','ought','used','to','of','in','on','at','by','for','with','about','against','between','into','through','during','before','after','above','below','from','up','down','out','off','over','under','again','further','here','there','now','then','than','too','very','just',
];

const FILIPINO = [
    'ang','mga','ng','sa','na','at','ay','ko','mo','niya','sila','kami','tayo','ito','iyan','iyon','para','kung','dahil','pero','o','kaya','lang','pa','naman','po','opo','salamat','magandang','umaga','hapon','gabi','araw','trabaho','trabahador','kandidato','kumpanya','kompanya','tulong','suporta','kakayahan','karanasan','edukasyon','lokasyon','sweldo','pasahod','kalusugan','kapansanan','abot','kamay',
];

const JOB_TITLES = [
    'accessibility specialist','accessibility auditor','accessibility engineer','accessibility consultant','accessibility tester','accessibility coordinator','accessibility manager','digital accessibility specialist','web accessibility specialist','assistive technology specialist','assistive technology trainer','screen reader specialist','braille transcriber','sign language interpreter','fsl interpreter','fsl relay associate','captioner','captioning specialist','disability support specialist','disability inclusion officer','disability advocate','inclusive design specialist','ux researcher','ux designer','ui designer','frontend developer','backend developer','full stack developer','software engineer','qa engineer','qa analyst','data analyst','data entry clerk','customer service representative','customer care associate','administrative assistant','office assistant','receptionist','clerk','encoder','transcriber','content writer','copywriter','virtual assistant','remote assistant','community manager','social media manager','hr specialist','hr assistant','recruiter','talent acquisition specialist','training specialist','trainer','coach','mentor','counselor','therapist','occupational therapist','physical therapist','speech therapist','psychologist','nurse','caregiver','personal assistant','aide','support worker','job coach','employment specialist','career counselor','vocational trainer','rehabilitation specialist',
];

const JOB_SEEKING = [
    'job','jobs','seeking','seek','seeker','candidate','candidates','applicant','applicants','application','applications','apply','applied','applying','hire','hiring','hired','employer','employers','company','companies','organization','recruit','recruiting','recruitment','recruiter','talent','vacancy','vacancies','opening','openings','position','positions','role','roles','opportunity','opportunities','career','careers','work','working','workplace','workforce','employment','employed','unemployed','resume','cv','curriculum','vitae','cover','letter','interview','interviews','interviewing','portfolio','skill','skills','qualification','qualifications','qualified','experience','experiences','education','training','certification','certifications','reference','references','salary','salaries','wage','wages','compensation','benefits','remote','hybrid','onsite','full-time','part-time','contract','freelance','internship','intern','probation','onboarding','orientation','offer','offers','accepted','rejected','shortlisted','reviewed','status','pending','approved','assessment','test','tests','exam','exams','screening','background','check','verification','verified','verify','documents','document','id','pwd','disability','accommodation','accommodations','accessible','accessibility','inclusive','inclusion','support','services','assistant','associate','specialist','analyst','engineer','developer','designer','manager','coordinator','technician','therapist','interpreter','transcriber','captioner','representative','customer','care','agent','clerk','admin','administrator','officer','staff','team','teams','department','division','branch','office','location','locations','city','cities','province','region','work','home','onsite','hybrid',
];

const ACCESSIBILITY_TECH = [
    'accessibility','auditor','specialist','analyst','engineer','developer','designer','manager','coordinator','assistant','associate','representative','technician','therapist','interpreter','transcriber','captioner','compliance','screen','reader','braille','sign','language','fsl','wcag','pa11y','axe','nvda','jaws','keyboard','navigation','contrast','font','dyslexia','motion','reduced','high','large','text','voice','input','predictive','autocomplete',
];

// Context -> word list
const VOCAB_BY_CONTEXT = {
    search: [...new Set([...JOB_TITLES, ...JOB_SEEKING, ...ACCESSIBILITY_TECH])],
    title: [...new Set([...JOB_TITLES, ...JOB_SEEKING])],
    skills: [...new Set(['accessibility','wcag','screen reader','braille','sign language','fsl','html','css','javascript','php','laravel','testing','qa','design','research','communication','leadership','teamwork','problem solving','time management','adaptability','creativity','analytical','customer service','data entry','encoding','transcription','captioning','interpreting'])],
    description: [...new Set([...COMMON, ...FILIPINO, ...JOB_SEEKING, ...ACCESSIBILITY_TECH])],
    default: [...new Set([...COMMON, ...FILIPINO])],
};

export function getContext(input) {
    if (!input) return 'default';
    if (input.getAttribute && input.getAttribute('data-predictive-context')) {
        return input.getAttribute('data-predictive-context');
    }
    const name = ((input.name || '') + ' ' + (input.id || '')).toLowerCase();
    const type = (input.type || '').toLowerCase();
    if (type === 'search') return 'search';
    if (name.includes('search') || name === 'q' || name.includes('query')) return 'search';
    if (name.includes('title')) return 'title';
    if (name.includes('skill')) return 'skills';
    if (input.tagName === 'TEXTAREA') return 'description';
    if (name.includes('description') || name.includes('bio') || name.includes('cover') || name.includes('resume') || name.includes('summary')) return 'description';
    return 'default';
}

export function getHistory() {
    try { return JSON.parse((typeof localStorage !== 'undefined' ? localStorage.getItem('pwd-e:word-history') : null) || '[]'); }
    catch (e) { return []; }
}

export function addHistory(word, context) {
    const h = getHistory();
    if (word && !h.includes(word)) {
        h.unshift(word);
        if (h.length > 200) h.length = 200;
        try { if (typeof localStorage !== 'undefined') localStorage.setItem('pwd-e:word-history', JSON.stringify(h)); } catch (e) {}
    }
}

export function allWords(context) {
    const ctx = context || 'default';
    const base = VOCAB_BY_CONTEXT[ctx] || VOCAB_BY_CONTEXT.default;
    const set = new Set(base);
    getHistory().forEach(w => set.add(w));
    return Array.from(set);
}

export function suggest(prefix, context) {
    if (!prefix || prefix.length < 2) return [];
    const p = prefix.toLowerCase();
    const words = allWords(context || 'default');
    const out = [];
    for (const w of words) {
        if (w.toLowerCase().startsWith(p) && w.toLowerCase() !== p) {
            out.push(w);
            if (out.length >= 5) break;
        }
    }
    return out;
}

export function currentWordInfo(value, pos) {
    pos = pos == null ? value.length : pos;
    const before = value.slice(0, pos);
    const m = before.match(/(\S+)$/);
    const word = m ? m[1] : '';
    const start = m ? pos - m[1].length : pos;
    return { word, start, pos };
}