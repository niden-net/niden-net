---
layout: post
title: Using Gource with GitHub commits
date: 2022-06-27T13:17:16.937Z
tags:
  - gource
  - github
  - visualization
---
Quite some time ago, I had the _brilliant_ idea to visualize the commits in the [Phalcon](https://github.com/phalcon/cphalcon) repository.

I watched the resulting video years ago when a colleague visualized the commits of the whole team for the project at the time. It was fascinating to see peoples names jump around here and there and making the whole code increase and progress.

The application to create a video of all (or some) of the commits in a repository is called [Gource](https://gource.io/).

After quite a bit of experimentation, I managed to get the perfect combination of parameters to make the videos. You can watch any of these videos in Phalcon's [Visualizations playlist](https://www.youtube.com/playlist?list=PLgJ0OtNTm8n89dPPpVgvnkgYN3lhRRDE2).

To create such a video you will need a few items. First and foremost, you need `gource` to be installed on your machine. Then (and this is my preference) you will need an image file (appearing at the bottom corner of the video). I have used the [logo--tablet](https://github.com/phalcon/assets/blob/master/phalcon/images/svg/logo--tablet.svg) from our [assets](https://github.com/phalcon/assets) repository.

The visualization is a two step process. First, I parse the repository and create a PPM file with all the data and then convert that to a MP4 file using `ffmpeg`

```
gource -1920x1080 \
    --stop-at-end \
    --multi-sampling \
    --highlight-users \
    --highlight-dirs \
    --auto-skip-seconds 1 \
    --file-filter \.png \
    --dir-colour 8b91a2 \
    --highlight-colour ffffff \
    --key \
    --bloom-multiplier 0.2 \
    --bloom-intensity 0.5 \
    --hide mouse,filenames \
    --seconds-per-day .5 \
    --dir-name-depth 1 \
    -r 60 \
    --logo ~/Downloads/logo--tablet.svg \
    --title "Visualizing commits for Phalcon PHP in 2021" \
    --start-date "2021-01-01 00:00:00" \
    --stop-date "2021-12-30 23:59:59" \
    -o ../2021.ppm 
```

The resulting PPM file will be quite big so you will need to have enough space to cater for that.

To produce the MP4 file:

```
ffmpeg -y \
    -r 60 \
    -f image2pipe \
    -vcodec ppm \
    -i 2021.ppm \
    -vcodec libx264 \
    -preset medium \
    -pix_fmt yuv420p \
    -crf 1 \
    -threads 0 \
    -bf 0 \
    2021.mp4
```

The audio used is from [Patrick Patrikios](https://www.youtube.com/channel/UCTPI2hZYxoHtdGEpdFoaU5A) and is called Forgiveness.