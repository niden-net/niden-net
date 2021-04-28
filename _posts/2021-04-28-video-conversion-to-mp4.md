---
layout: post
title: Video Conversion to MP4
date: 2021-04-28T14:04:30.684Z
tags:
  - video
  - mp4
  - mov
  - webm
  - conversion
---
Recently I have put some effort in organizing some of our video files. These are mostly home movies, kids doing stuff, places we visited etc.

I run however in a small snag. At some point we used to own Apple devices and the videos of the time were .MOV files, and some of them were really big in size. I therefore decided to convert everything into one format MP4. 

For our latest phones (that is what we use primarily for recording said videos), that format is pre-selected to MP4 so no problem there. 

Using Linux as my primary workstation, the operation is dead simple. All I had to do is use [ffmpeg](https://ffmpeg.org/download.html#get-sources).

The command I used is:

```
ffmpeg -i kids.mov -vcodec h264 -acodec mp2 kids.mp4
```

The above will convert `kids.mov` to `kids.mp4`. Of course you can change the path of the input file and/or the output file.

For a MOV file slightly above 20MB I ended up with a MP4 file just below 4MB.