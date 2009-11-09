from django.http import HttpResponse
from synckit.views import *
from django.template import Context, loader
from clientlogger.models import *
import json

def main(request):
    t = loader.get_template('runner.html')
    c = Context({})
    return HttpResponse(t.render(c))

def perfgen(request):
    pageid = request.GET["id"]
    entry = LogEntry.objects.all().filter(id = int(pageid))[0]
    allentries = LogEntry.objects.all().filter(
        tester = entry.tester, 
        tester_comments = entry.tester_comments,
        test_file = entry.test_file,
        style = entry.style
    )
    for e in allentries:
        e.params = e.params.replace('\\"', '"')
        e.params = e.params.replace('filter":["', 'filter":[')
        e.params = e.params.replace('"]',']')
        e.url = e.url.replace(' ', '%20')
        if e.style == 'synckit':
            e.url = '/wiki/synckit'
            e.params = e.params.replace("{\"queries\":\"", "queries=")
            e.params = e.params.replace("\",\"latency\":\"", "&latency=")
            e.params = e.params.replace("\",\"bandwidth\":\"", "&bandwidth=")
            e.params = e.params.rstrip("}\"")
        elif e.style == 'flying':
            e.params = e.params.replace("{\"queries\":\"", "queries=")
            e.params = e.params.rstrip("}")
            e.params = e.params.rstrip("\"")
            e.url = '/wiki/tokyo'
        
    entries = []
    cached = 0
    uncached = 0
    for e in allentries:
        if (e.params == 'CACHED'):
            cached = cached + 1
        else:
            uncached = uncached + 1
            entries.append(e)
    
    t = loader.get_template('perfgen.html')
    c = Context({
        "cached":cached,
        "total":(uncached+cached),
        "cpercnet":(float(cached)/(float(cached)+float(uncached))),
        "entry":entry,
        "entries":entries
    })
    return HttpResponse(t.render(c))
